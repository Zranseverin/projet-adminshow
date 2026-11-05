<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\MarketplaceSetting;
use App\Models\Product;
use App\Models\Seller;
use App\Models\SellerOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class MarketplaceTest extends TestCase
{
    use RefreshDatabase;

    private function seller(): Seller
    {
        $user = User::factory()->create();
        $user->account_type = 'seller';
        $user->save();

        return Seller::create(['user_id' => $user->id, 'name' => 'Boutique '.Str::random(5), 'slug' => Str::uuid(),
            'phone' => '0102030405', 'payment_instructions' => 'Virement au vendeur']);
    }

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->account_type = 'admin';
        $user->save();

        return $user;
    }

    private function product(?Seller $seller = null, string $status = 'published'): Product
    {
        return Product::create(['seller_id' => $seller?->id, 'name' => 'Sac en cuir', 'price_minor' => 1250000, 'stock' => 10, 'status' => $status]);
    }

    private function payload(array $products, string $mode = 'platform'): array
    {
        return ['customer_name' => 'Client Test', 'customer_phone' => '0700000000', 'address' => 'Abidjan', 'payment_mode' => $mode,
            'request_key' => (string) Str::uuid(), 'items' => array_map(fn ($p) => ['product_id' => $p->id, 'quantity' => 2], $products)];
    }

    public function test_registration_does_not_grant_admin_and_seller_cannot_access_admin_pages(): void
    {
        $this->post('/devenir-vendeur', ['name' => 'Alice', 'shop_name' => 'Alice Boutique', 'email' => 'alice@example.com',
            'phone' => '0700000000', 'password' => 'password12345', 'password_confirmation' => 'password12345', 'account_type' => 'admin'])
            ->assertRedirect(route('seller.products.index'));
        $this->assertDatabaseHas('users', ['email' => 'alice@example.com', 'account_type' => 'seller']);
        $this->get('/admin/books')->assertForbidden();
        $this->get('/marketplace/settings')->assertForbidden();
        $this->get('/dashboard')->assertForbidden();
    }

    public function test_migration_preserves_books_and_imports_them_to_the_general_catalog(): void
    {
        $book = Book::create(['title' => 'Livre historique', 'slug' => 'livre-historique', 'price' => 15500, 'stock_quantity' => 52, 'is_active' => true]);
        $migration = require database_path('migrations/2026_11_05_000001_create_marketplace.php');
        $migration->down();
        $migration->up();
        $this->assertDatabaseHas('books', ['id' => $book->id, 'title' => 'Livre historique']);
        $this->assertDatabaseHas('products', ['legacy_book_id' => $book->id, 'name' => 'Livre historique', 'price_minor' => 1550000, 'stock' => 52, 'status' => 'published']);
    }

    public function test_product_ownership_and_moderation_are_enforced(): void
    {
        $seller = $this->seller();
        $other = $this->seller();
        $product = $this->product($other);
        $this->actingAs($seller->user)->get(route('seller.products.edit', $product))->assertNotFound();
        $this->put(route('seller.products.update', $product), ['name' => 'Hijack'])->assertNotFound();
        $this->post(route('seller.products.store'), ['name' => 'T-shirt', 'price' => 4500, 'stock' => 4, 'status' => 'submit', 'seller_id' => $other->id])->assertRedirect();
        $this->assertDatabaseHas('products', ['name' => 'T-shirt', 'seller_id' => $seller->id, 'status' => 'pending']);
        $this->getJson('/api/marketplace/products')->assertJsonMissing(['name' => 'T-shirt']);
        $seller->update(['publication_mode' => 'immediate']);
        $this->actingAs($seller->user->fresh());
        $this->post(route('seller.products.store'), ['name' => 'Pantalon', 'price' => 9000, 'stock' => 3, 'status' => 'submit'])->assertRedirect();
        $this->assertDatabaseHas('products', ['name' => 'Pantalon', 'status' => 'published']);
    }

    public function test_suspended_sellers_and_pending_products_are_hidden(): void
    {
        $seller = $this->seller();
        $product = $this->product($seller);
        $pending = $this->product(null, 'pending');
        $seller->update(['is_active' => false]);
        $this->getJson('/api/marketplace/products')->assertJsonCount(0, 'data');
        $this->getJson('/api/marketplace/products/'.$product->id)->assertNotFound();
        $this->getJson('/api/marketplace/products/'.$pending->id)->assertNotFound();
        $this->actingAs($seller->user)->get('/vendeur/products')->assertForbidden();
    }

    public function test_platform_order_splits_sellers_snapshots_commission_and_stays_unpaid(): void
    {
        MarketplaceSetting::current()->update(['commission_bps' => 1000, 'payment_instructions' => 'Compte plateforme']);
        $first = $this->product($this->seller());
        $second = $this->product($this->seller());
        $payload = $this->payload([$first, $second]);
        $payload['paid'] = true;
        $payload['price'] = 1;
        $response = $this->postJson('/api/marketplace/orders', $payload)->assertCreated()->assertJsonPath('data.total', 50000)->assertJsonPath('data.payment_status', 'pending')->assertJsonCount(2, 'data.sellers');
        $this->assertDatabaseCount('market_orders', 1);
        $this->assertDatabaseCount('seller_orders', 2);
        $this->assertDatabaseHas('seller_orders', ['total_minor' => 2500000, 'commission_minor' => 250000, 'seller_due_minor' => 2250000, 'payment_status' => 'pending']);
        $this->assertSame(8, $first->fresh()->stock);
        $this->postJson('/api/marketplace/orders', $payload)->assertCreated()->assertJsonPath('data.reference', $response->json('data.reference'));
        $this->assertDatabaseCount('market_orders', 1);
        $this->assertSame(8, $first->fresh()->stock);
        MarketplaceSetting::current()->update(['commission_bps' => 2000]);
        $this->assertDatabaseHas('seller_orders', ['commission_bps' => 1000]);
    }

    public function test_direct_order_has_no_commission_and_returns_seller_instructions(): void
    {
        MarketplaceSetting::current()->update(['commission_bps' => 1000]);
        $product = $this->product($this->seller());
        $this->postJson('/api/marketplace/orders', $this->payload([$product], 'direct'))->assertCreated()->assertJsonPath('data.sellers.0.payment_instructions', 'Virement au vendeur');
        $this->assertDatabaseHas('seller_orders', ['commission_minor' => 0, 'seller_due_minor' => 2500000]);
    }

    public function test_stock_or_payment_configuration_errors_roll_back_the_whole_order(): void
    {
        MarketplaceSetting::current()->update(['payment_instructions' => 'Compte plateforme']);
        $first = $this->product($this->seller());
        $second = $this->product($this->seller());
        $second->update(['stock' => 1]);
        $this->postJson('/api/marketplace/orders', $this->payload([$first, $second]))->assertUnprocessable();
        $this->assertDatabaseCount('market_orders', 0);
        $this->assertSame(10, $first->fresh()->stock);
        MarketplaceSetting::current()->update(['platform_payment' => false]);
        $this->postJson('/api/marketplace/orders', $this->payload([$first]))->assertUnprocessable();
        $this->assertDatabaseCount('market_orders', 0);
    }

    public function test_seller_cannot_confirm_platform_payment_or_access_another_sellers_orders(): void
    {
        MarketplaceSetting::current()->update(['payment_instructions' => 'Compte plateforme']);
        $seller = $this->seller();
        $other = $this->seller();
        $this->postJson('/api/marketplace/orders', $this->payload([$this->product($seller)]))->assertCreated();
        $order = SellerOrder::firstOrFail();
        $this->actingAs($seller->user)->put(route('seller.orders.update', $order), ['action' => 'payment', 'reference' => 'fake'])->assertForbidden();
        $this->actingAs($other->user)->put(route('seller.orders.update', $order), ['action' => 'status', 'status' => 'delivered'])->assertNotFound();
        $this->get(route('seller.orders'))->assertOk()->assertDontSee('Client Test');
        $this->actingAs($this->admin())->put(route('marketplace.orders.update', $order), ['action' => 'payout', 'reference' => 'early'])->assertUnprocessable();
        $this->put(route('marketplace.orders.update', $order), ['action' => 'payment', 'reference' => 'bank-123'])->assertRedirect();
        $this->put(route('marketplace.orders.update', $order), ['action' => 'payout', 'reference' => 'transfer-456'])->assertRedirect();
        $this->assertNotNull($order->fresh()->settled_at);
        $this->put(route('marketplace.orders.update', $order), ['action' => 'payout', 'reference' => 'duplicate'])->assertUnprocessable();
    }

    public function test_cancellation_restores_stock_exactly_once(): void
    {
        $seller = $this->seller();
        $product = $this->product($seller);
        $this->postJson('/api/marketplace/orders', $this->payload([$product], 'direct'))->assertCreated();
        $this->assertSame(8, $product->fresh()->stock);
        $order = SellerOrder::firstOrFail();
        $this->actingAs($seller->user)->put(route('seller.orders.update', $order), ['action' => 'cancel'])->assertRedirect();
        $this->assertSame(10, $product->fresh()->stock);
        $this->put(route('seller.orders.update', $order), ['action' => 'cancel'])->assertUnprocessable();
        $this->assertSame(10, $product->fresh()->stock);
    }

    public function test_shipped_order_cannot_be_reverted_or_cancelled(): void
    {
        $seller = $this->seller();
        $product = $this->product($seller);
        $this->postJson('/api/marketplace/orders', $this->payload([$product], 'direct'))->assertCreated();
        $order = SellerOrder::firstOrFail();
        $this->actingAs($seller->user)->put(route('seller.orders.update', $order), ['action' => 'status', 'status' => 'shipped'])->assertRedirect();
        $this->put(route('seller.orders.update', $order), ['action' => 'status', 'status' => 'pending'])->assertUnprocessable();
        $this->put(route('seller.orders.update', $order), ['action' => 'cancel'])->assertUnprocessable();
        $this->assertSame(8, $product->fresh()->stock);
    }

    public function test_management_views_render_and_admin_can_approve_product(): void
    {
        $seller = $this->seller();
        $product = $this->product($seller, 'pending');
        $this->actingAs($this->admin());
        foreach (['/marketplace/products', '/marketplace/products/create', '/marketplace/products/'.$product->id.'/edit', '/marketplace/settings', '/marketplace/sellers', '/marketplace/orders'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->put(route('marketplace.products.update', $product), ['name' => 'Sac validé', 'price' => 12500, 'stock' => 10, 'status' => 'published'])->assertRedirect();
        $this->getJson('/api/marketplace/products/'.$product->id)->assertOk()->assertJsonPath('data.name', 'Sac validé');
        $this->actingAs($seller->user);
        foreach (['/vendeur/products', '/vendeur/products/create', '/vendeur/products/'.$product->id.'/edit', '/vendeur/boutique', '/vendeur/commandes'] as $url) {
            $this->get($url)->assertOk();
        }
    }
}
