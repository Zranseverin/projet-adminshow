<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleOAuthSetting extends Model
{
    protected $guarded = ['id'];

    protected $hidden = ['client_secret'];

    protected function casts(): array
    {
        return ['client_secret' => 'encrypted'];
    }

    public static function current(): self
    {
        return static::query()->first() ?? new static([
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'redirect_uri' => config('services.google.redirect'),
        ]);
    }
}
