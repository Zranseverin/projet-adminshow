<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table): void {
            $table->string('publisher')->nullable()->after('description');
            $table->date('publication_date')->nullable()->after('publisher');
            $table->unsignedInteger('page_count')->nullable()->after('publication_date');
            $table->string('dimensions', 80)->nullable()->after('page_count');
            $table->string('translator')->nullable()->after('dimensions');
            $table->string('language', 80)->nullable()->after('translator');
            $table->string('collection')->nullable()->after('language');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table): void {
            $table->dropColumn([
                'publisher',
                'publication_date',
                'page_count',
                'dimensions',
                'translator',
                'language',
                'collection',
            ]);
        });
    }
};
