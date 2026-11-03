<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'payment_method',
        'screenshot_path',
        'transaction_reference',
        'status',
        'verified_at',
        'verified_by',
        'admin_note',
        'advance_amount',
        'balance_amount',
        'advance_percentage',
        'delivery_screenshot_path',
        'delivery_transaction_reference',
        'delivery_paid_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'advance_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
        'advance_percentage' => 'integer',
        'delivery_paid_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
