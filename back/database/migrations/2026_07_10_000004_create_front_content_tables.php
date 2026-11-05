<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('headers', function (Blueprint $table) {
            $table->id();
            $table->string('logo_name', 120)->default('Chapitre');
            $table->text('logo_icon')->nullable();
            $table->string('search_placeholder', 180)->default('Rechercher un livre, un auteur...');
            $table->json('nav_items')->nullable();
            $table->string('hero_eyebrow', 180)->nullable();
            $table->string('hero_title', 220)->nullable();
            $table->string('hero_title_emphasis', 160)->nullable();
            $table->string('hero_subtitle', 220)->nullable();
            $table->string('hero_promo', 220)->nullable();
            $table->string('hero_cta_label', 120)->nullable();
            $table->string('hero_cta_page', 80)->default('catalogue');
            $table->string('hero_main_book_page', 80)->default('product');
            $table->string('hero_main_book_bg')->nullable();
            $table->json('hero_small_books')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('title', 180);
            $table->string('subtitle')->nullable();
            $table->string('brand', 120)->nullable();
            $table->string('placement', 80);
            $table->string('image_url')->nullable();
            $table->string('background')->nullable();
            $table->string('cta_label', 120)->nullable();
            $table->string('cta_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->index(['placement', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertisements');
        Schema::dropIfExists('headers');
    }
};
