<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketOrderItem extends Model
{
    public function sellerOrder()
    {
        return $this->belongsTo(SellerOrder::class);
    }
    public $timestamps = false;

    protected $guarded = ['id'];
}
