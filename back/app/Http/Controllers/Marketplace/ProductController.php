<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MarketplaceSetting;
use App\Models\Product;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    private function admin(Request $request): bool
    {
        return $request->user()->account_type === 'admin';
    }

    private function authorizeProduct(Request $request, Product $product): void
    {
        abort_unless($this->admin($request) || $product->seller_id === $request->user()->seller->id, 404);
    }

    private function prefix(Request $request): string
    {
        return $this->admin($request) ? 'marketplace' : 'seller';
    }

    public function index(Request $request)
    {
        $products = Product::with(['seller', 'category'])
            ->when(! $this->admin($request), fn ($q) => $q->where('seller_id', $request->user()->seller->id))
            ->when($request->input('q'), fn ($q, $search) => $q->where('name', 'like', '%'.$search.'%'))
            ->when($request->input('status'), fn ($q, $status) => $q->where('status', $status))
            ->latest()->paginate(20)->withQueryString();

        return view('marketplace.products', ['products' => $products, 'prefix' => $this->prefix($request)]);
    }

    public function create(Request $request)
    {
        return $this->form($request, new Product(['stock' => 0, 'status' => 'draft']));
    }

    public function edit(Request $request, Product $product)
    {
        $this->authorizeProduct($request, $product);

        return $this->form($request, $product);
    }

    private function form(Request $request, Product $product)
    {
        return view('marketplace.product-form', ['product' => $product, 'prefix' => $this->prefix($request),
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
            'sellers' => $this->admin($request) ? Seller::where('is_active', true)->orderBy('name')->get() : collect()]);
    }

    public function store(Request $request)
    {
        return $this->save($request, new Product);
    }

    public function update(Request $request, Product $product)
    {
        $this->authorizeProduct($request, $product);

        return $this->save($request, $product);
    }

    private function save(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:220', 'description' => 'nullable|string|max:20000',
            'category_id' => ['nullable', Rule::exists('categories', 'id')->where('is_active', true)],
            'brand' => 'nullable|string|max:120', 'sku' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0|max:999999999', 'old_price' => 'nullable|numeric|min:0|max:999999999',
            'stock' => 'required|integer|min:0|max:10000000', 'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'attribute_names' => 'nullable|array|max:12', 'attribute_names.*' => 'nullable|string|max:80',
            'attribute_values' => 'nullable|array|max:12', 'attribute_values.*' => 'nullable|string|max:255',
            'status' => ['required', Rule::in($this->admin($request) ? ['draft', 'pending', 'published', 'rejected'] : ['draft', 'submit'])],
            'seller_id' => ['nullable', Rule::exists('sellers', 'id')->where('is_active', true)],
            'review_note' => 'nullable|string|max:2000',
        ]);
        $sellerId = $this->admin($request) ? ($product->exists ? $product->seller_id : ($data['seller_id'] ?? null)) : $request->user()->seller->id;
        $status = $data['status'];
        if (! $this->admin($request) && $status === 'submit') {
            $mode = $request->user()->seller->publication_mode;
            $mode = $mode === 'inherit' ? MarketplaceSetting::current()->publication_mode : $mode;
            $status = $mode === 'immediate' ? 'published' : 'pending';
        }
        $attributes = [];
        foreach ($data['attribute_names'] ?? [] as $i => $key) {
            if (filled($key) && filled($data['attribute_values'][$i] ?? null)) {
                $attributes[trim($key)] = trim($data['attribute_values'][$i]);
            }
        }
        $product->fill(collect($data)->only(['name', 'description', 'category_id', 'brand', 'sku', 'stock'])->all());
        $product->seller_id = $sellerId;
        $product->price_minor = (int) round((float) $data['price'] * 100);
        $product->old_price_minor = isset($data['old_price']) ? (int) round((float) $data['old_price'] * 100) : null;
        $product->attributes = $attributes;
        $product->status = $status;
        $product->review_note = $this->admin($request) ? ($data['review_note'] ?? null) : null;
        if ($request->hasFile('photo')) {
            $product->image = $request->file('photo')->store('products', 'public');
        }
        $product->save();

        return redirect()->route($this->prefix($request).'.products.index')->with('success', $status === 'pending' ? 'Produit soumis pour validation.' : 'Produit enregistré.');
    }
}
