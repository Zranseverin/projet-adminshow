<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketOrder extends Model
{
    protected $guarded = ['id'];

    public function sellerOrders()
    {
        return $this->hasMany(SellerOrder::class);
    }
}
