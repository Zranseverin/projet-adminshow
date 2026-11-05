<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    public function getPublicImageUrlAttribute(): ?string
    {
        if (! $this->image_url) {
            return null;
        }

        return str_starts_with($this->image_url, 'http://') || str_starts_with($this->image_url, 'https://')
            ? $this->image_url : route('media.show', ['path' => $this->image_url]);
    }

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
