<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    protected $fillable = [
        'name',
        'slug',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'book_categories');
    }
}
