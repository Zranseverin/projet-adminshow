<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function members()
    {
        return $this->hasMany(User::class, 'member_seller_id');
    }

    public function orders()
    {
        return $this->hasMany(SellerOrder::class);
    }
}
