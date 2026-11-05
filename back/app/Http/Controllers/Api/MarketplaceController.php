<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MarketOrder;
use App\Models\MarketplaceSetting;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MarketplaceController extends Controller
{
    private function serializeProduct(Product $product): array
    {
        return ['id' => $product->id, 'name' => $product->name, 'description' => $product->description,
            'image_url' => $product->image_url, 'brand' => $product->brand, 'sku' => $product->sku,
            'attributes' => $product->attributes ?? [], 'price' => $product->price_minor / 100,
            'old_price' => $product->old_price_minor === null ? null : $product->old_price_minor / 100,
            'stock' => $product->stock, 'category' => $product->category?->name,
            'seller' => ['id' => $product->seller_id, 'name' => $product->seller?->name ?? 'Boutique de la plateforme',
                'slug' => $product->seller?->slug, 'description' => $product->seller?->description]];
    }

    public function index(Request $request)
    {
        $data = $request->validate(['q' => 'nullable|string|max:200', 'category' => 'nullable|integer',
            'categories' => 'nullable|array|max:50', 'categories.*' => 'integer|min:1',
            'sort' => 'nullable|in:newest,popular,price_asc,price_desc', 'sale' => 'nullable|boolean',
            'seller' => 'nullable|string|max:200', 'page' => 'nullable|integer|min:1']);
        $products = Product::visible()->with(['category', 'seller'])
            ->when($data['q'] ?? null, fn ($q, $search) => $q->where('name', 'like', '%'.$search.'%'))
            ->when($data['category'] ?? null, fn ($q, $id) => $q->where('category_id', $id))
            ->when($data['categories'] ?? [], fn ($q, $ids) => $q->whereIn('category_id', $ids))
            ->when($request->boolean('sale'), fn ($q) => $q->whereColumn('old_price_minor', '>', 'price_minor'))
            ->when($data['seller'] ?? null, fn ($q, $slug) => $q->whereHas('seller', fn ($s) => $s->where('slug', $slug)))
            ->when(($data['sort'] ?? '') === 'popular', fn ($q) => $q->withSum(['orderItems as sold_quantity' => fn ($items) => $items->whereHas('sellerOrder', fn ($orders) => $orders->where('payment_status', 'paid')->where('status', '!=', 'cancelled'))], 'quantity')->orderByDesc('sold_quantity'))
            ->when(($data['sort'] ?? '') === 'price_asc', fn ($q) => $q->orderBy('price_minor'))
            ->when(($data['sort'] ?? '') === 'price_desc', fn ($q) => $q->orderByDesc('price_minor'))
            ->latest('products.id')->paginate(24);

        return response()->json(['data' => $products->map(fn ($p) => $this->serializeProduct($p)),
            'meta' => ['current_page' => $products->currentPage(), 'last_page' => $products->lastPage(), 'total' => $products->total()]]);
    }

    public function show(Product $product)
    {
        abort_unless(Product::visible()->whereKey($product->id)->exists(), 404);

        return response()->json(['data' => $this->serializeProduct($product->load(['seller', 'category']))]);
    }

    public function options()
    {
        $settings = MarketplaceSetting::current();
        $advertisements = \App\Models\Advertisement::whereRaw("placement LIKE ? ESCAPE '!'", ['marketplace!_catalog!_%'])->where('is_active', true)
            ->whereNotNull('image_url')->orderBy('sort_order')->orderBy('id')->get()
            ->map(fn ($advertisement) => ['id' => $advertisement->id, 'title' => $advertisement->title,
                'image_url' => $advertisement->public_image_url, 'button_url' => $advertisement->button_url]);

        return response()->json(['data' => ['categories' => Category::where('is_active', true)->select(['id', 'name'])->withCount(['products' => fn ($q) => $q->visible()])->orderBy('name')->get(),
            'payment_modes' => array_values(array_filter([$settings->platform_payment ? 'platform' : null, $settings->direct_payment ? 'direct' : null])),
            'advertisements' => $advertisements,
            'seller_url' => route('seller.register'), 'login_url' => route('login')]]);
    }

    public function checkout(Request $request)
    {
        $data = $request->validate([
            'customer_name' => 'required|string|max:180', 'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'required|string|max:40', 'address' => 'required|string|max:2000',
            'payment_mode' => 'required|in:platform,direct', 'items' => 'required|array|min:1|max:50',
            'request_key' => 'required|uuid',
            'items.*.product_id' => 'required|integer|distinct', 'items.*.quantity' => 'required|integer|min:1|max:100',
        ]);
        $hash = hash('sha256', json_encode($data));
        $existing = MarketOrder::where('request_key', $data['request_key'])->first();
        if ($existing) {
            abort_unless(hash_equals($existing->request_hash, $hash), 409, 'Cette demande correspond déjà à une autre commande.');

            return $this->orderResponse($existing->load('sellerOrders.items'));
        }
        $order = DB::transaction(function () use ($data, $hash) {
            $settings = MarketplaceSetting::current();
            $platform = $data['payment_mode'] === 'platform';
            abort_unless($platform ? $settings->platform_payment : $settings->direct_payment, 422, 'Ce mode de paiement est désactivé.');
            $products = Product::visible()->with('seller')->whereIn('id', array_column($data['items'], 'product_id'))->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            abort_unless($products->count() === count($data['items']), 422, 'Un produit du panier est indisponible.');
            $order = MarketOrder::create(collect($data)->except('items')->all() + ['reference' => (string) Str::uuid(), 'request_hash' => $hash, 'total_minor' => 0]);
            $groups = [];
            foreach ($data['items'] as $item) {
                $product = $products[$item['product_id']];
                abort_if($product->stock < $item['quantity'], 422, 'Stock insuffisant : '.$product->name);
                $key = $product->seller_id ?? 0;
                $groups[$key][] = ['product' => $product, 'quantity' => $item['quantity']];
            }
            $total = 0;
            foreach ($groups as $lines) {
                $seller = $lines[0]['product']->seller;
                $instructions = $platform || ! $seller ? $settings->payment_instructions : $seller->payment_instructions;
                abort_unless(filled($instructions), 422, 'Les instructions de paiement ne sont pas encore configurées pour '.($seller?->name ?? 'la plateforme').'.');
                $subtotal = array_sum(array_map(fn ($line) => $line['product']->price_minor * $line['quantity'], $lines));
                $bps = $platform && $seller ? $settings->commission_bps : 0;
                $commission = intdiv($subtotal * $bps + 5000, 10000);
                $part = $order->sellerOrders()->create(['seller_id' => $seller?->id, 'seller_name' => $seller?->name ?? 'Boutique de la plateforme',
                    'payment_instructions' => $instructions, 'total_minor' => $subtotal, 'commission_bps' => $bps,
                    'commission_minor' => $commission, 'seller_due_minor' => $subtotal - $commission]);
                foreach ($lines as $line) {
                    $part->items()->create(['product_id' => $line['product']->id, 'name' => $line['product']->name,
                        'quantity' => $line['quantity'], 'unit_price_minor' => $line['product']->price_minor,
                        'total_minor' => $line['product']->price_minor * $line['quantity']]);
                    \App\Support\ProductStock::change($line['product'], -$line['quantity'], 'sale', 'Commande '.$order->reference);
                }
                $total += $subtotal;
            }
            $order->update(['total_minor' => $total]);

            return $order->load('sellerOrders.items');
        });

        return $this->orderResponse($order);
    }

    private function orderResponse(MarketOrder $order)
    {
        // No client declaration is accepted as proof of payment.
        return response()->json(['data' => ['reference' => $order->reference, 'total' => $order->total_minor / 100,
            'payment_mode' => $order->payment_mode, 'payment_status' => 'pending',
            'sellers' => $order->sellerOrders->map(fn ($part) => ['name' => $part->seller_name, 'total' => $part->total_minor / 100,
                'payment_instructions' => $part->payment_instructions, 'items' => $part->items->map(fn ($line) => ['name' => $line->name, 'quantity' => $line->quantity])])]], 201);
    }
}
