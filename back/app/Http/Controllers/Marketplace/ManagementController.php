<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceSetting;
use App\Models\Product;
use App\Models\Seller;
use App\Models\SellerOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManagementController extends Controller
{
    public function settings()
    {
        return view('marketplace.settings', ['settings' => MarketplaceSetting::current()]);
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate(['publication_mode' => 'required|in:review,immediate',
            'commission' => 'required|numeric|min:0|max:100', 'payment_instructions' => 'nullable|string|max:4000',
            'platform_payment' => 'nullable|boolean', 'direct_payment' => 'nullable|boolean']);
        if (! $request->boolean('platform_payment') && ! $request->boolean('direct_payment')) {
            return back()->withErrors(['payment' => 'Activez au moins un mode de paiement.'])->withInput();
        }
        MarketplaceSetting::current()->update(['publication_mode' => $data['publication_mode'],
            'commission_bps' => (int) round((float) $data['commission'] * 100), 'payment_instructions' => $data['payment_instructions'] ?? null,
            'platform_payment' => $request->boolean('platform_payment'), 'direct_payment' => $request->boolean('direct_payment')]);

        return back()->with('success', 'Paramètres enregistrés.');
    }

    public function sellers()
    {
        return view('marketplace.sellers', ['sellers' => Seller::with('user')->withCount('products')->latest()->paginate(20)]);
    }

    public function updateSeller(Request $request, Seller $seller)
    {
        $data = $request->validate(['publication_mode' => 'required|in:inherit,review,immediate', 'is_active' => 'required|boolean']);
        $seller->update($data);

        return back()->with('success', 'Boutique mise à jour.');
    }

    public function shop(Request $request)
    {
        return view('marketplace.shop', ['seller' => $request->user()->seller]);
    }

    public function updateShop(Request $request)
    {
        $request->user()->seller->update($request->validate(['name' => 'required|string|max:160', 'phone' => 'required|string|max:40',
            'description' => 'nullable|string|max:4000', 'payment_instructions' => 'nullable|string|max:4000']));

        return back()->with('success', 'Boutique enregistrée.');
    }

    public function orders(Request $request)
    {
        $orders = SellerOrder::with(['marketOrder', 'items'])
            ->when($request->user()->account_type === 'admin' && $request->filled('seller_id'), fn ($q) => $q->where('seller_id', $request->input('seller_id')))
            ->when($request->user()->account_type !== 'admin', fn ($q) => $q->where('seller_id', $request->user()->seller->id))
            ->latest()->paginate(15);

        return view('marketplace.orders', compact('orders'));
    }

    public function updateOrder(Request $request, SellerOrder $sellerOrder)
    {
        $admin = $request->user()->account_type === 'admin';
        abort_unless($admin || $sellerOrder->seller_id === $request->user()->seller->id, 404);
        $data = $request->validate(['action' => 'required|in:status,payment,payout,cancel', 'status' => 'nullable|in:pending,processing,shipped,delivered',
            'reference' => 'nullable|string|max:255']);
        DB::transaction(function () use ($data, $sellerOrder, $admin, $request) {
            $order = SellerOrder::with('marketOrder')->lockForUpdate()->findOrFail($sellerOrder->id);
            abort_if($order->status === 'cancelled', 422, 'Cette commande est annulée.');
            if ($data['action'] === 'status') {
                abort_unless(isset($data['status']), 422);
                $rank = ['pending' => 0, 'processing' => 1, 'shipped' => 2, 'delivered' => 3];
                abort_if($rank[$data['status']] < $rank[$order->status], 422, 'Une commande ne peut pas revenir à une étape précédente.');
                $order->update(['status' => $data['status']]);
            } elseif ($data['action'] === 'payment') {
                abort_unless($admin || $order->marketOrder->payment_mode === 'direct', 403);
                abort_unless(filled($data['reference'] ?? null), 422, 'Une référence de paiement est obligatoire.');
                abort_unless($order->payment_status === 'pending', 422, 'Paiement déjà confirmé.');
                $order->update(['payment_status' => 'paid', 'payment_reference' => $data['reference'], 'paid_at' => now()]);
            } elseif ($data['action'] === 'cancel') {
                abort_unless($order->payment_status === 'pending' && in_array($order->status, ['pending', 'processing'], true), 422, 'Seule une commande non payée et non expédiée peut être annulée.');
                foreach ($order->items()->orderBy('product_id')->get() as $item) {
                    if ($item->product_id) {
                        $product = Product::whereKey($item->product_id)->lockForUpdate()->firstOrFail();
                        \App\Support\ProductStock::change($product, $item->quantity, 'cancellation', 'Annulation '.$order->marketOrder->reference, $request->user()->id);
                    }
                }
                $order->update(['status' => 'cancelled']);
            } else {
                abort_unless($admin, 403);
                abort_unless($order->seller_id && $order->marketOrder->payment_mode === 'platform' && $order->payment_status === 'paid' && ! $order->settled_at, 422);
                abort_unless(filled($data['reference'] ?? null), 422, 'Une référence de reversement est obligatoire.');
                $order->update(['payout_reference' => $data['reference'], 'settled_at' => now()]);
            }
        });

        return back()->with('success', 'Commande mise à jour.');
    }
}
