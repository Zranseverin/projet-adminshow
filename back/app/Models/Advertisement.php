<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    protected $fillable = [
        'placement',
        'label',
        'title',
        'subtitle',
        'image_url',
        'button_text',
        'button_url',
        'background',
        'button_background',
        'button_color',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
