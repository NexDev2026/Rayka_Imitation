<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_sku',
        'product_image',
        'variant_info',
        'unit_price',
        'quantity',
        'subtotal',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity' => 'integer',
        'subtotal' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Resolve product image URL dynamically based on current host/request.
     */
    public function getProductImageAttribute(?string $value): ?string
    {
        if (empty($value)) {
            return $this->product?->effective_primary_image ?? asset('images/no-image-placeholder.svg');
        }

        // If it contains a full URL, extract path if it's a local storage/upload/image path
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            $parsed = parse_url($value);
            if (! empty($parsed['path'])) {
                $path = $parsed['path'];
                if (str_starts_with($path, '/storage/') || str_starts_with($path, '/uploads/') || str_starts_with($path, '/images/') || str_starts_with($path, '/build/')) {
                    return asset(ltrim($path, '/'));
                }
            }

            return $value;
        }

        return asset(ltrim($value, '/'));
    }
}
