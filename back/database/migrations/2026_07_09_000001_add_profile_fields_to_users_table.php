<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('code')->unique()->nullable()->after('id');
            $table->string('name_complet')->nullable()->after('name');
            $table->string('telephone')->nullable()->after('name_complet');
            $table->string('adresse')->nullable()->after('email');
            $table->foreignId('role_id')->nullable()->after('adresse');
            $table->foreignId('user_id')->nullable()->after('role_id');
            $table->string('user_code')->nullable()->after('user_id');
            $table->foreignId('genre_id')->nullable()->after('user_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'code',
                'name_complet',
                'telephone',
                'adresse',
                'role_id',
                'user_id',
                'user_code',
                'genre_id',
            ]);
        });
    }
};
