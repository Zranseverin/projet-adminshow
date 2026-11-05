<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('advertisements')->updateOrInsert(
            ['placement' => 'sticky_bottom'],
            [
                'label' => 'Sponsorise - Fnac',
                'title' => 'Fnac+ - Livraison illimitee offerte',
                'subtitle' => 'Commandez autant que vous voulez, sans frais de livraison.',
                'image_url' => 'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=180&q=80',
                'button_text' => 'Decouvrir l offre',
                'button_url' => 'https://www.fnac.com/',
                'background' => '#0F1F3D',
                'button_background' => 'var(--amber)',
                'button_color' => 'white',
                'is_active' => true,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('advertisements')->where('placement', 'sticky_bottom')->delete();
    }
};
