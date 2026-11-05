<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function orderItems()
    {
        return $this->hasMany(MarketOrderItem::class);
    }
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['attributes' => 'array', 'price_minor' => 'integer', 'stock' => 'integer'];
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where(fn ($q) => $q->whereNull('seller_id')->orWhereHas('seller', fn ($s) => $s->where('is_active', true)))
            ->where(fn ($q) => $q->whereNull('category_id')->orWhereHas('category', fn ($c) => $c->where('is_active', true)));
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        return str_starts_with($this->image, 'https://') || str_starts_with($this->image, 'http://')
            ? $this->image : route('media.show', ['path' => $this->image]);
    }
}
