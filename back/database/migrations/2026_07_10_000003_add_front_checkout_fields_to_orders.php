<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_address')->nullable()->after('customer_phone');
            $table->string('shipping_city', 100)->nullable()->after('shipping_address');
            $table->string('shipping_country', 100)->nullable()->after('shipping_city');
            $table->string('payment_method', 80)->nullable()->after('payment_status');
            $table->string('payment_reference', 120)->nullable()->after('payment_method');
            $table->timestamp('paid_at')->nullable()->after('payment_reference');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('author_name', 180)->nullable()->after('book_title');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('author_name');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_address',
                'shipping_city',
                'shipping_country',
                'payment_method',
                'payment_reference',
                'paid_at',
            ]);
        });
    }
};
