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

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.marketplace.checkout_key' => 'checkout-test-key']);
        $this->withHeaders(['X-Marketplace-Key' => 'checkout-test-key', 'X-Customer-Id' => '1']);
    }

    public function test_checkout_rejects_requests_without_authenticated_frontend_customer(): void
    {
        $this->withHeaders(['X-Marketplace-Key' => '', 'X-Customer-Id' => ''])->postJson('/api/marketplace/orders', [])->assertUnauthorized();
        $this->assertDatabaseCount('market_orders', 0);
    }

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

    public function test_catalog_category_counts_sorting_and_promotions_use_visible_products(): void
    {
        $category = \App\Models\Category::create(['name' => 'Accessoires', 'slug' => 'accessoires-test', 'is_active' => true]);
        $cheap = $this->product();
        $cheap->update(['category_id' => $category->id, 'price_minor' => 50000, 'old_price_minor' => 70000]);
        $expensive = $this->product();
        $expensive->update(['category_id' => $category->id]);
        $hidden = $this->product(null, 'pending');
        $hidden->update(['category_id' => $category->id]);
        $this->getJson('/api/marketplace/products?sort=price_asc&categories[]='.$category->id)
            ->assertOk()->assertJsonCount(2, 'data')->assertJsonPath('data.0.id', $cheap->id);
        $this->getJson('/api/marketplace/products?sort=price_desc')->assertOk()->assertJsonPath('data.0.id', $expensive->id);
        $this->getJson('/api/marketplace/products?sale=1')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $cheap->id);
        $categories = collect($this->getJson('/api/marketplace/options')->assertOk()->json('data.categories'));
        $this->assertSame(2, $categories->firstWhere('id', $category->id)['products_count']);
        $this->getJson('/api/marketplace/products?sort=popular')->assertOk();
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
        $this->assertDatabaseHas('product_stock_movements', ['product_id' => $product->id, 'seller_id' => $seller->id, 'quantity' => -2, 'type' => 'sale', 'stock_before' => 10, 'stock_after' => 8]);
        $this->assertDatabaseHas('product_stock_movements', ['product_id' => $product->id, 'seller_id' => $seller->id, 'quantity' => 2, 'type' => 'cancellation', 'stock_before' => 8, 'stock_after' => 10]);
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
        foreach (['/vendeur/tableau-de-bord', '/vendeur/products', '/vendeur/products/create', '/vendeur/products/'.$product->id.'/edit', '/vendeur/boutique', '/vendeur/commandes', '/vendeur/stock'] as $url) {
            $this->get($url)->assertOk()->assertSee('class="dash-sidebar"', false)->assertSee('aria-current="page"', false);
        }
    }

    public function test_inventory_is_private_and_adjustments_are_logged_without_negative_stock(): void
    {
        $seller = $this->seller(); $other = $this->seller();
        $own = $this->product($seller); $foreign = $this->product($other);
        $own->update(['name' => 'Produit prive Alice']);
        $foreign->update(['name' => 'Produit prive Bob']);
        $this->actingAs($seller->user)->get(route('seller.inventory', ['seller_id' => $other->id]))
            ->assertOk()->assertSee('Produit prive Alice')->assertDontSee('Produit prive Bob');
        $this->put(route('seller.inventory.update', $foreign), ['type' => 'entry', 'quantity' => 4, 'reason' => 'Intrusion'])->assertNotFound();
        $this->put(route('seller.inventory.update', $own), ['type' => 'entry', 'quantity' => 5, 'reason' => 'Reapprovisionnement'])->assertRedirect();
        $this->assertSame(15, $own->fresh()->stock);
        $this->assertDatabaseHas('product_stock_movements', ['product_id' => $own->id, 'seller_id' => $seller->id, 'quantity' => 5, 'stock_before' => 10, 'stock_after' => 15]);
        $this->put(route('seller.inventory.update', $own), ['type' => 'exit', 'quantity' => 16, 'reason' => 'Trop de sorties'])->assertUnprocessable();
        $this->assertSame(15, $own->fresh()->stock);
        $this->assertDatabaseCount('product_stock_movements', 1);
        $this->put(route('seller.products.update', $own), ['name' => 'Produit prive Alice', 'price' => 12500, 'stock' => 900, 'status' => 'draft'])->assertRedirect();
        $this->assertSame(15, $own->fresh()->stock);
        $this->actingAs($other->user)->get(route('seller.inventory'))->assertOk()->assertDontSee('Reapprovisionnement');
        $this->actingAs($this->admin())->get(route('marketplace.inventory', ['seller_id' => $seller->id]))->assertOk()->assertSee('Produit prive Alice')->assertDontSee('Produit prive Bob');
    }

    public function test_static_dashboard_shows_demo_content_and_preserves_shop_access(): void
    {
        $seller = $this->seller(); $other = $this->seller();
        $foreign = $this->product($other);
        $foreign->update(['name' => 'Produit secret autre boutique']);
        $this->actingAs($seller->user)->get(route('seller.dashboard'))->assertOk()
            ->assertSee('demo-badge', false)->assertSee('1 573 620')
            ->assertSee('Sac week-end')->assertSee('Revenus cumul')
            ->assertDontSee('Produit secret autre boutique');
        $this->get(route('marketplace.sellers.dashboard', $other))->assertForbidden();
        $this->actingAs($this->admin())->get(route('marketplace.sellers.dashboard', $seller))->assertOk()
            ->assertSee($seller->name)->assertSee('demo-badge', false);
    }

    public function test_advertising_is_admin_only_and_active_announcements_are_published_in_order(): void
    {
        $seller = $this->seller();
        $this->actingAs($seller->user)->get('/marketplace/advertisements')->assertForbidden();
        $this->post('/marketplace/advertisements', ['title' => 'Intrusion'])->assertForbidden();
        $this->actingAs($this->admin());
        $this->get('/marketplace/advertisements')->assertOk();
        $this->get('/marketplace/advertisements/create')->assertOk();
        $this->post('/marketplace/advertisements', ['title' => 'Annonce B', 'image_url' => 'https://example.com/banner.png',
            'button_url' => 'https://example.com/offer', 'sort_order' => 20, 'is_active' => 1])->assertRedirect();
        $ad = \App\Models\Advertisement::where('title', 'Annonce B')->firstOrFail();
        $this->get(route('marketplace.advertisements.edit', $ad))->assertOk();
        $this->put(route('marketplace.advertisements.update', $ad), ['title' => 'Annonce A', 'sort_order' => 1, 'is_active' => 1])->assertRedirect();
        $this->assertSame('https://example.com/banner.png', $ad->fresh()->image_url);
        $this->getJson('/api/marketplace/options')->assertOk()->assertJsonPath('data.advertisements.0.title', 'Annonce A');
        $this->put(route('marketplace.advertisements.update', $ad), ['title' => 'Annonce A', 'sort_order' => 1, 'is_active' => 0])->assertRedirect();
        $this->getJson('/api/marketplace/options')->assertJsonCount(0, 'data.advertisements');
        $this->actingAs($seller->user)->delete(route('marketplace.advertisements.destroy', $ad))->assertForbidden();
        $this->actingAs($this->admin())->delete(route('marketplace.advertisements.destroy', $ad))->assertRedirect();
        $this->assertDatabaseMissing('advertisements', ['id' => $ad->id]);
    }

    public function test_advertisement_upload_replacement_and_deletion_manage_files(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $this->actingAs($this->admin());
        $this->post('/marketplace/advertisements', ['title' => 'Image annonce', 'sort_order' => 0, 'is_active' => 1,
            'image' => \Illuminate\Http\UploadedFile::fake()->image('banner.png')])->assertRedirect();
        $ad = \App\Models\Advertisement::where('title', 'Image annonce')->firstOrFail();
        $oldImage = $ad->image_url;
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($oldImage);
        $this->getJson('/api/marketplace/options')->assertJsonPath('data.advertisements.0.image_url', route('media.show', ['path' => $oldImage]));
        $this->put(route('marketplace.advertisements.update', $ad), ['title' => 'Image modifiee', 'sort_order' => 0, 'is_active' => 1,
            'image' => \Illuminate\Http\UploadedFile::fake()->image('replacement.png')])->assertRedirect();
        \Illuminate\Support\Facades\Storage::disk('public')->assertMissing($oldImage);
        $newImage = $ad->fresh()->image_url;
        $this->delete(route('marketplace.advertisements.destroy', $ad))->assertRedirect();
        \Illuminate\Support\Facades\Storage::disk('public')->assertMissing($newImage);
    }
}
