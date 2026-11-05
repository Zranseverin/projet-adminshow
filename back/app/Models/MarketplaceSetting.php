<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketplaceSetting extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['platform_payment' => 'boolean', 'direct_payment' => 'boolean'];
    }

    public static function current(): self
    {
        return static::findOrFail(1);
    }
}
