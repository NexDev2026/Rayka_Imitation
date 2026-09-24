<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'image_url',
        'is_primary',
        'is_secondary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_secondary' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getUrlAttribute(): string
    {
        if (empty($this->image_url)) {
            return asset('images/no-image-placeholder.svg');
        }
        if (str_starts_with($this->image_url, 'http://') || str_starts_with($this->image_url, 'https://')) {
            return $this->image_url;
        }

        return asset(ltrim($this->image_url, '/'));
    }
}
