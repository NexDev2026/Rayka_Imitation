<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_value',
        'max_discount',
        'usage_limit',
        'used_count',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_value' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'expires_at' => 'date',
        'is_active' => 'boolean',
    ];

    public function isValidForAmount(float $amount): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        if ($amount < $this->min_order_value) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $amount): float
    {
        if (! $this->isValidForAmount($amount)) {
            return 0.0;
        }

        if ($this->type === 'percentage') {
            $discount = ($amount * $this->value) / 100;
            if ($this->max_discount && $discount > $this->max_discount) {
                return (float) $this->max_discount;
            }

            return (float) $discount;
        }

        return (float) min($this->value, $amount);
    }
}
