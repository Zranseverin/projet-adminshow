<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('account_type', 20)->default('seller')->index();
        });
        // Existing accounts retain their administration access; registration never grants it.
        DB::table('users')->update(['account_type' => 'admin']);

        Schema::create('marketplace_settings', function (Blueprint $table) {
            $table->id();
            $table->string('publication_mode')->default('review');
            $table->boolean('platform_payment')->default(true);
            $table->boolean('direct_payment')->default(true);
            $table->unsignedInteger('commission_bps')->default(0);
            $table->text('payment_instructions')->nullable();
            $table->timestamps();
        });
        DB::table('marketplace_settings')->insert(['id' => 1, 'publication_mode' => 'review', 'platform_payment' => true, 'direct_payment' => true, 'commission_bps' => 0, 'created_at' => now(), 'updated_at' => now()]);
        foreach (['mode-accessoires' => 'Mode et accessoires', 'maison-decoration' => 'Maison et décoration', 'high-tech' => 'High-tech', 'beaute-soins' => 'Beauté et soins', 'sports-loisirs' => 'Sports et loisirs', 'livres-culture' => 'Livres et culture'] as $slug => $name) {
            DB::table('categories')->insertOrIgnore(['name' => $name, 'slug' => $slug, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
        }

        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->restrictOnDelete();
            $table->string('name', 160);
            $table->string('slug', 200)->unique();
            $table->string('phone', 40);
            $table->text('description')->nullable();
            $table->text('payment_instructions')->nullable();
            $table->string('publication_mode')->default('inherit');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('legacy_book_id')->nullable()->unique()->constrained('books')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 220);
            $table->text('description')->nullable();
            $table->string('image', 500)->nullable();
            $table->string('brand', 120)->nullable();
            $table->string('sku', 100)->nullable();
            $table->json('attributes')->nullable();
            $table->unsignedBigInteger('price_minor');
            $table->unsignedBigInteger('old_price_minor')->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->string('status')->default('pending')->index();
            $table->text('review_note')->nullable();
            $table->timestamps();
        });
        DB::table('books')->orderBy('id')->chunkById(100, function ($books) {
            foreach ($books as $book) {
                DB::table('products')->insert([
                    'legacy_book_id' => $book->id,
                    'category_id' => DB::table('book_categories')->where('book_id', $book->id)->value('category_id'),
                    'name' => $book->title, 'description' => $book->description, 'image' => $book->cover_image,
                    'sku' => $book->isbn, 'brand' => $book->publisher,
                    'attributes' => json_encode(array_filter(['Format' => $book->format, 'Langue' => $book->language, 'Collection' => $book->collection, 'ISBN' => $book->isbn])),
                    'price_minor' => (int) round((float) $book->price * 100),
                    'old_price_minor' => $book->old_price === null ? null : (int) round((float) $book->old_price * 100),
                    'stock' => $book->stock_quantity, 'status' => $book->is_active ? 'published' : 'draft',
                    'created_at' => $book->created_at, 'updated_at' => $book->updated_at,
                ]);
            }
        });
        Schema::create('market_orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('reference')->unique();
            $table->uuid('request_key')->unique();
            $table->string('request_hash', 64);
            $table->string('customer_name', 180);
            $table->string('customer_email')->nullable();
            $table->string('customer_phone', 40);
            $table->text('address');
            $table->string('payment_mode');
            $table->unsignedBigInteger('total_minor');
            $table->timestamps();
        });
        Schema::create('seller_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('market_order_id')->constrained()->restrictOnDelete();
            $table->foreignId('seller_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('seller_name');
            $table->text('payment_instructions')->nullable();
            $table->unsignedBigInteger('total_minor');
            $table->unsignedInteger('commission_bps')->default(0);
            $table->unsignedBigInteger('commission_minor')->default(0);
            $table->unsignedBigInteger('seller_due_minor');
            $table->string('status')->default('pending');
            $table->string('payment_status')->default('pending');
            $table->string('payment_reference')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('payout_reference')->nullable();
            $table->timestamp('settled_at')->nullable();
            $table->timestamps();
        });
        Schema::create('market_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_order_id')->constrained()->restrictOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('unit_price_minor');
            $table->unsignedBigInteger('total_minor');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('market_order_items');
        Schema::dropIfExists('seller_orders');
        Schema::dropIfExists('market_orders');
        Schema::dropIfExists('products');
        Schema::dropIfExists('sellers');
        Schema::dropIfExists('marketplace_settings');
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['account_type']);
            $table->dropColumn('account_type');
        });
    }
};
