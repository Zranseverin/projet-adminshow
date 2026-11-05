<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_options', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 80)->unique();
            $table->string('name', 160);
            $table->string('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        DB::table('delivery_options')->insert([
            [
                'code' => 'pickup',
                'name' => 'Retrait en boutique',
                'description' => 'Le client recupere sa commande sur place.',
                'price' => 0,
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'standard',
                'name' => 'Livraison standard',
                'description' => 'Livraison locale en 2 a 4 jours ouvres.',
                'price' => 2000,
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'distance',
                'name' => 'Livraison a distance',
                'description' => 'Pour les communes ou villes eloignees.',
                'price' => 5000,
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'express',
                'name' => 'Livraison express',
                'description' => 'Livraison prioritaire selon disponibilite.',
                'price' => 3500,
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_options');
    }
};
