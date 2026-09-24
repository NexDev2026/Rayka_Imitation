<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inquiry extends Model
{
    protected $fillable = [
        'type',
        'name',
        'email',
        'mobile',
        'phone',
        'company',
        'subject',
        'message',
        'status',
        'source',
        'assigned_to',
        'priority',
        'follow_up_at',
        'admin_notes',
        'is_read',
    ];

    protected $casts = [
        'follow_up_at' => 'datetime',
        'is_read' => 'boolean',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(InquiryItem::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
