<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductStockMovement;
use App\Support\ProductStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $admin = $request->user()->account_type === 'admin';
        $data = $request->validate(['q' => 'nullable|string|max:200', 'availability' => 'nullable|in:low,out', 'seller_id' => 'nullable|integer|min:1']);
        $query = Product::query()->when(! $admin, fn ($q) => $q->where('seller_id', $request->user()->seller->id))
            ->when($admin && isset($data['seller_id']), fn ($q) => $q->where('seller_id', $data['seller_id']));
        $summary = ['products' => (clone $query)->count(), 'units' => (clone $query)->sum('stock'),
            'low' => (clone $query)->whereBetween('stock', [1, 5])->count(), 'out' => (clone $query)->where('stock', 0)->count()];
        $products = (clone $query)->with('seller')
            ->when($data['q'] ?? null, fn ($q, $term) => $q->where(fn ($q) => $q->where('name', 'like', '%'.$term.'%')->orWhere('sku', 'like', '%'.$term.'%')))
            ->when(($data['availability'] ?? '') === 'low', fn ($q) => $q->whereBetween('stock', [1, 5]))
            ->when(($data['availability'] ?? '') === 'out', fn ($q) => $q->where('stock', 0))
            ->orderBy('stock')->orderBy('name')->paginate(20)->withQueryString();
        $movements = ProductStockMovement::with(['product', 'user'])
            ->whereIn('product_id', (clone $query)->select('id'))->latest('id')->limit(30)->get();
        return view('marketplace.inventory', compact('products', 'summary', 'movements', 'admin'));
    }
    public function update(Request $request, Product $product)
    {
        $admin = $request->user()->account_type === 'admin';
        abort_unless($admin || $product->seller_id === $request->user()->seller->id, 404);
        $data = $request->validate(['type' => 'required|in:entry,exit', 'quantity' => 'required|integer|min:1|max:10000000', 'reason' => 'required|string|max:255']);
        DB::transaction(function () use ($product, $data, $request, $admin) {
            $locked = Product::whereKey($product->id)->when(! $admin, fn ($q) => $q->where('seller_id', $request->user()->seller->id))->lockForUpdate()->firstOrFail();
            ProductStock::change($locked, ($data['type'] === 'entry' ? 1 : -1) * (int) $data['quantity'], $data['type'], $data['reason'], $request->user()->id);
        });
        return back()->with('success', 'Mouvement enregistré. Le stock de la boutique est à jour.');
    }
}
