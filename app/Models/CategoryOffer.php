<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CategoryOffer extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'category_id',
        'discount_percentage',
        'starts_at',
        'ends_at',
        'is_active',
        'badge_text',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'discount_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_offer_category');
    }

    public function getCategoryNamesAttribute(): string
    {
        $names = $this->categories->pluck('name');
        if ($names->isEmpty() && $this->category) {
            return $this->category->name;
        }

        return $names->isNotEmpty() ? $names->implode(', ') : 'All Categories';
    }

    public function scopeActive(Builder $query): Builder
    {
        $now = now();

        return $query->where('is_active', true)
            ->where('starts_at', '<=', $now)
            ->where('ends_at', '>=', $now);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where('starts_at', '>', now());
    }

    public function getIsLiveAttribute(): bool
    {
        $now = now();

        return $this->is_active && $this->starts_at <= $now && $this->ends_at >= $now;
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->is_active && $this->starts_at > now();
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->ends_at < now();
    }

    public function getStatusLabelAttribute(): string
    {
        if (! $this->is_active) {
            return 'Inactive';
        }
        if ($this->is_live) {
            return 'Live Now';
        }
        if ($this->is_upcoming) {
            return 'Upcoming';
        }

        return 'Expired';
    }
}
