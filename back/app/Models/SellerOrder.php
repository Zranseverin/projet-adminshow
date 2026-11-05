<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerOrder extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['paid_at' => 'datetime', 'settled_at' => 'datetime'];
    }

    public function marketOrder()
    {
        return $this->belongsTo(MarketOrder::class);
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function items()
    {
        return $this->hasMany(MarketOrderItem::class);
    }
}
