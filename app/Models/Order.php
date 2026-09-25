<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Log;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'guest_token',
        'address_id',
        'subtotal',
        'coupon_discount',
        'coupon_code',
        'shipping_fee',
        'total_amount',
        'status',
        'cancelled_by',
        'cancellation_reason',
        'cancelled_at',
        'tracking_carrier',
        'tracking_number',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'coupon_discount' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'cancelled_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * Resolves the order shipping address, with fallback to user's saved address.
     */
    public function getShippingAddressAttribute(): ?Address
    {
        if ($this->address) {
            return $this->address;
        }

        if ($this->user) {
            return $this->user->addresses()->where('is_default', true)->first()
                ?? $this->user->addresses()->latest()->first();
        }

        return null;
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function isPendingVerification(): bool
    {
        return $this->status === 'Pending Verification';
    }

    public function isConfirmed(): bool
    {
        return in_array($this->status, ['Confirmed', 'Processing', 'Shipped', 'Delivered']);
    }

    public function isProcessing(): bool
    {
        return $this->status === 'Processing';
    }

    public function isShipped(): bool
    {
        return $this->status === 'Shipped';
    }

    public function isDelivered(): bool
    {
        return $this->status === 'Delivered';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'Cancelled';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['Pending Verification', 'Confirmed', 'Processing']);
    }

    public function isCancelledByCustomer(): bool
    {
        if ($this->status !== 'Cancelled') {
            return false;
        }

        if ($this->cancelled_by !== null) {
            return $this->cancelled_by === 'customer';
        }

        return $this->getLegacyCancellationActor() === 'customer';
    }

    public function isCancelledByAdmin(): bool
    {
        if ($this->status !== 'Cancelled') {
            return false;
        }

        if ($this->cancelled_by !== null) {
            return $this->cancelled_by === 'admin';
        }

        return $this->getLegacyCancellationActor() === 'admin';
    }

    public function isRejected(): bool
    {
        return $this->status === 'Rejected';
    }

    /**
     * Professional E-Commerce SaaS Protection Guard:
     * Active orders (Pending Verification, Confirmed, Processing, Shipped, Delivered) are protected.
     * Only orders in 'Cancelled' or 'Rejected' status can be permanently deleted.
     */
    public function canBeDeleted(): bool
    {
        return in_array(strtolower((string) $this->status), ['cancelled', 'rejected']);
    }

    /**
     * Resolves legacy cancellation actor from notes when cancelled_by column is null.
     */
    public function getLegacyCancellationActor(): string
    {
        $notes = $this->notes ?? '';
        $adminNote = $this->payment?->admin_note ?? '';
        $combined = $notes . "\n" . $adminNote;
        $lines = array_filter(array_map('trim', explode("\n", $combined)));

        foreach (array_reverse($lines) as $line) {
            if (preg_match('/(?:Cancelled|Order cancelled)\s+by\s+(?:user|customer)/i', $line)) {
                return 'customer';
            }
            if (preg_match('/(?:Cancelled|Rejected|Order marked as Cancelled|Order marked as Rejected|Payment & Order rejected)\s+by\s+(?:administrator|admin)/i', $line)) {
                return 'admin';
            }
        }

        return 'customer';
    }

    public function getCancellationDetails(): array
    {
        $byCustomer = $this->isCancelledByCustomer();
        $reason = $this->cancellation_reason;
        $cancelledAt = $this->cancelled_at ?? $this->updated_at;

        if (! $reason) {
            $notes = $this->notes ?? '';
            $adminNote = $this->payment?->admin_note ?? '';
            $combined = $notes . "\n" . $adminNote;
            $lines = array_filter(array_map('trim', explode("\n", $combined)));

            foreach (array_reverse($lines) as $line) {
                if (preg_match('/Reason:\s*(.+)$/i', $line, $matches)) {
                    $reason = trim($matches[1]);
                    break;
                }
            }
        }

        if (! $reason) {
            $reason = $byCustomer ? 'Order cancelled by user.' : 'Order cancelled by administrator.';
        }

        return [
            'by_customer' => $byCustomer,
            'cancelled_at' => $cancelledAt,
            'reason' => $reason,
        ];
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'Pending Verification' => 'bg-amber-100 text-amber-900 border-amber-300',
            'Confirmed' => 'bg-emerald-100 text-emerald-900 border-emerald-300',
            'Processing' => 'bg-blue-100 text-blue-900 border-blue-300',
            'Shipped' => 'bg-indigo-100 text-indigo-900 border-indigo-300',
            'Delivered' => 'bg-green-100 text-green-900 border-green-300',
            'Rejected' => 'bg-rose-100 text-rose-900 border-rose-300',
            'Cancelled' => $this->isCancelledByCustomer() ? 'bg-stone-100 text-stone-700 border-stone-300' : 'bg-rose-50 text-rose-800 border-rose-200',
            default => 'bg-stone-100 text-stone-900 border-stone-300',
        };
    }

    public function getStepIndexAttribute(): int
    {
        return match ($this->status) {
            'Pending Verification' => 1,
            'Confirmed' => 2,
            'Processing' => 3,
            'Shipped' => 4,
            'Delivered' => 5,
            'Rejected', 'Cancelled' => 0,
            default => 1,
        };
    }

    public function restoreInventory(): void
    {
        $this->loadMissing('items.product');
        foreach ($this->items as $item) {
            $qty = max(1, (int) $item->quantity);
            if ($item->product) {
                $item->product->increment('stock_quantity', $qty);
                Log::info("Inventory Restored: +{$qty} unit(s) for Product #{$item->product_id} ('{$item->product_name}') via Order #{$this->order_number} cancellation/rejection.");

                ActivityLog::record(
                    action: 'INVENTORY_RESTORED',
                    description: "Restored +{$qty} unit(s) of '{$item->product_name}' back to available stock (Order #{$this->order_number}).",
                    category: 'inventory',
                    subjectType: 'Product',
                    subjectId: (string) $item->product_id,
                    subjectRef: $item->product_sku ?: $this->order_number,
                    metadata: [
                        'order_number' => $this->order_number,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product_name,
                        'quantity_restored' => $qty,
                        'variant' => $item->variant_info,
                    ]
                );
            }
            if ($item->variant_info && $item->product_id) {
                $variant = ProductVariant::where('product_id', $item->product_id)
                    ->where('value', $item->variant_info)
                    ->first();
                if ($variant) {
                    $variant->increment('stock_quantity', $qty);
                    Log::info("Variant Inventory Restored: +{$qty} unit(s) for Variant '{$item->variant_info}' on Product #{$item->product_id} via Order #{$this->order_number}.");
                }
            }
        }
    }

    public function deductInventory(): void
    {
        $this->loadMissing('items.product');
        foreach ($this->items as $item) {
            $qty = max(1, (int) $item->quantity);
            if ($item->product) {
                $item->product->decrement('stock_quantity', $qty);
                Log::info("Inventory Deducted: -{$qty} unit(s) for Product #{$item->product_id} ('{$item->product_name}') via Order #{$this->order_number} re-activation.");

                ActivityLog::record(
                    action: 'INVENTORY_DEDUCTED',
                    description: "Deducted -{$qty} unit(s) of '{$item->product_name}' from available stock (Order #{$this->order_number}).",
                    category: 'inventory',
                    subjectType: 'Product',
                    subjectId: (string) $item->product_id,
                    subjectRef: $item->product_sku ?: $this->order_number,
                    metadata: [
                        'order_number' => $this->order_number,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product_name,
                        'quantity_deducted' => $qty,
                        'variant' => $item->variant_info,
                    ]
                );
            }
            if ($item->variant_info && $item->product_id) {
                $variant = ProductVariant::where('product_id', $item->product_id)
                    ->where('value', $item->variant_info)
                    ->first();
                if ($variant) {
                    $variant->decrement('stock_quantity', $qty);
                    Log::info("Variant Inventory Deducted: -{$qty} unit(s) for Variant '{$item->variant_info}' on Product #{$item->product_id} via Order #{$this->order_number}.");
                }
            }
        }
    }

    public function getTrackingUrlAttribute(): ?string
    {
        if (! $this->tracking_number) {
            return null;
        }

        $carrier = strtolower($this->tracking_carrier ?? '');
        $awb = urlencode($this->tracking_number);

        if (str_contains($carrier, 'delhivery')) {
            return "https://www.delhivery.com/track/package/{$awb}";
        }
        if (str_contains($carrier, 'blue dart') || str_contains($carrier, 'bluedart')) {
            return 'https://www.bluedart.com/tracking';
        }
        if (str_contains($carrier, 'dtdc')) {
            return 'https://www.dtdc.in/tracking/shipment-tracking.asp';
        }
        if (str_contains($carrier, 'post') || str_contains($carrier, 'india post')) {
            return 'https://www.indiapost.gov.in/_layouts/15/dpt.cept.tracking/trackconsignment.aspx';
        }
        if (str_contains($carrier, 'shiprocket')) {
            return "https://shiprocket.co/tracking/{$awb}";
        }
        if (str_contains($carrier, 'shadowfax')) {
            return "https://tracker.shadowfax.in/#/track/{$awb}";
        }
        if (str_contains($carrier, 'ecom')) {
            return "https://ecomexpress.in/tracking/?awb_field={$awb}";
        }

        return 'https://www.google.com/search?q='.urlencode(($this->tracking_carrier ?: 'courier')." {$this->tracking_number} tracking");
    }
}
