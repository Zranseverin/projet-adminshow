<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('member_seller_id')->nullable()->constrained('sellers')->restrictOnDelete();
            $table->boolean('member_is_active')->default(true);
        });
    }
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('member_seller_id');
            $table->dropColumn('member_is_active');
        });
    }
};
