<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('mail_settings', function (Blueprint $table) {
            $table->foreignId('seller_id')->nullable()->unique()->constrained('sellers')->cascadeOnDelete();
        });
    }
    public function down(): void
    {
        Schema::table('mail_settings', fn (Blueprint $table) => $table->dropConstrainedForeignId('seller_id'));
    }
};
