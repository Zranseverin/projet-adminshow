<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('advertisements')) {
            Schema::create('advertisements', function (Blueprint $table): void {
                $table->id();
                $table->string('placement', 80)->unique();
                $table->string('label', 160)->nullable();
                $table->string('title', 220);
                $table->text('subtitle')->nullable();
                $table->string('image_url', 700)->nullable();
                $table->string('button_text', 120)->nullable();
                $table->string('button_url', 700)->nullable();
                $table->string('background', 255)->nullable();
                $table->string('button_background', 120)->nullable();
                $table->string('button_color', 80)->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        Schema::table('advertisements', function (Blueprint $table): void {
            if (! Schema::hasColumn('advertisements', 'placement')) {
                $table->string('placement', 80)->unique()->after('id');
            }
            if (! Schema::hasColumn('advertisements', 'label')) {
                $table->string('label', 160)->nullable()->after('placement');
            }
            if (! Schema::hasColumn('advertisements', 'title')) {
                $table->string('title', 220)->after('label');
            }
            if (! Schema::hasColumn('advertisements', 'subtitle')) {
                $table->text('subtitle')->nullable()->after('title');
            }
            if (! Schema::hasColumn('advertisements', 'image_url')) {
                $table->string('image_url', 700)->nullable()->after('subtitle');
            }
            if (! Schema::hasColumn('advertisements', 'button_text')) {
                $table->string('button_text', 120)->nullable()->after('image_url');
            }
            if (! Schema::hasColumn('advertisements', 'button_url')) {
                $table->string('button_url', 700)->nullable()->after('button_text');
            }
            if (! Schema::hasColumn('advertisements', 'background')) {
                $table->string('background', 255)->nullable()->after('button_url');
            }
            if (! Schema::hasColumn('advertisements', 'button_background')) {
                $table->string('button_background', 120)->nullable()->after('background');
            }
            if (! Schema::hasColumn('advertisements', 'button_color')) {
                $table->string('button_color', 80)->nullable()->after('button_background');
            }
            if (! Schema::hasColumn('advertisements', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('button_color');
            }
            if (! Schema::hasColumn('advertisements', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('is_active');
            }
            if (! Schema::hasColumn('advertisements', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (! Schema::hasColumn('advertisements', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });

        collect([
            [
                'placement' => 'home_banner',
                'label' => 'Sponsorise - Amazon',
                'title' => 'Kindle Unlimited - 3 mois offerts',
                'subtitle' => "Accedez a plus d'1 million de livres, magazines et podcasts. Sans engagement. Resiliez a tout moment.",
                'image_url' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=420&q=80',
                'button_text' => 'Essayer gratuitement',
                'button_url' => 'https://www.amazon.fr/kindle-dbs/hz/signup',
                'background' => 'linear-gradient(135deg,#1A2744 0%,#2C4A8C 100%)',
                'button_background' => 'var(--amber)',
                'button_color' => 'white',
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'placement' => 'home_duo_left',
                'label' => 'Sponsorise - Audible',
                'title' => 'Vos livres preferes en audio',
                'subtitle' => '1 credit offert par mois. Ecoutez partout, meme hors connexion.',
                'image_url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=720&q=80',
                'button_text' => '1 mois gratuit',
                'button_url' => 'https://www.audible.fr/',
                'background' => 'white',
                'button_background' => 'var(--amber)',
                'button_color' => 'white',
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'placement' => 'home_duo_right',
                'label' => 'Sponsorise - Cultura',
                'title' => 'Carte cadeau Cultura - des 5 000 FCFA',
                'subtitle' => 'Livres, vinyles, papeterie... Le cadeau ideal pour tous les passionnes de culture.',
                'image_url' => 'https://images.unsplash.com/photo-1513201099705-a9746e1e201f?auto=format&fit=crop&w=720&q=80',
                'button_text' => 'Offrir maintenant',
                'button_url' => 'https://www.cultura.com/',
                'background' => 'white',
                'button_background' => '#E8003D',
                'button_color' => 'white',
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'placement' => 'catalog_sidebar',
                'label' => 'Sponsorise - Babelio',
                'title' => 'Babelio Plus',
                'subtitle' => 'Critiques exclusives et alertes nouveautes.',
                'image_url' => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&w=520&q=80',
                'button_text' => 'En savoir plus',
                'button_url' => 'https://www.babelio.com/',
                'background' => 'white',
                'button_background' => '#2D6A4F',
                'button_color' => 'white',
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ])->each(function (array $advertisement): void {
            DB::table('advertisements')->updateOrInsert(
                ['placement' => $advertisement['placement']],
                $advertisement
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
