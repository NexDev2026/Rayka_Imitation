<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'mobile',
        'email',
        'address_line',
        'street',
        'road',
        'landmark',
        'city',
        'state',
        'pincode',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getFormattedAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_line,
            $this->street,
            $this->road,
            $this->landmark ? "Near {$this->landmark}" : null,
            $this->city,
            "{$this->state} - {$this->pincode}",
        ]);

        return implode(', ', $parts);
    }

    /**
     * Normalize a text string (e.g. address line, street, city) for duplicate detection.
     */
    public static function normalizeString(?string $str): string
    {
        if (! $str) {
            return '';
        }

        // Convert to lowercase
        $s = mb_strtolower(trim($str));

        // Strip address tags like (home), (office), (other)
        $s = preg_replace('/\((home|office|other)\)/i', '', $s);

        // Strip punctuation and special characters
        $s = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $s);

        // Collapse multiple whitespace
        return trim(preg_replace('/\s+/', ' ', $s));
    }

    /**
     * Normalize city name (e.g. "Gandhi Nagar" vs "Gandhinagar").
     */
    public static function normalizeCity(?string $city): string
    {
        if (! $city) {
            return '';
        }

        return preg_replace('/\s+/', '', mb_strtolower(trim($city)));
    }

    /**
     * Generate a unique normalized key for an address instance or array.
     */
    public static function normalizeKey($address): string
    {
        $pincode = is_array($address) ? ($address['pincode'] ?? '') : ($address->pincode ?? '');
        $addressLine = is_array($address) ? ($address['address_line'] ?? '') : ($address->address_line ?? '');
        $city = is_array($address) ? ($address['city'] ?? '') : ($address->city ?? '');

        return trim($pincode) . '|' . self::normalizeCity($city) . '|' . self::normalizeString($addressLine);
    }

    /**
     * Find an existing matching address for a user.
     */
    public static function findMatchingForUser(int $userId, array $attributes): ?self
    {
        $pincode = trim($attributes['pincode'] ?? '');
        if (! $pincode) {
            return null;
        }

        $candidates = self::where('user_id', $userId)
            ->where('pincode', $pincode)
            ->get();

        if ($candidates->isEmpty()) {
            return null;
        }

        $targetKey = self::normalizeKey($attributes);
        $targetNormLine = self::normalizeString($attributes['address_line'] ?? '');
        $targetNormStreet = self::normalizeString($attributes['street'] ?? '');
        $targetNormCity = self::normalizeCity($attributes['city'] ?? '');

        foreach ($candidates as $candidate) {
            $candidateKey = self::normalizeKey($candidate);
            if ($candidateKey === $targetKey) {
                return $candidate;
            }

            // Check if city matches and address lines or streets match closely within same pincode
            $candNormCity = self::normalizeCity($candidate->city);
            if ($candNormCity && $targetNormCity && $candNormCity === $targetNormCity) {
                $candNormLine = self::normalizeString($candidate->address_line);
                $candNormStreet = self::normalizeString($candidate->street);

                if ($candNormLine && $targetNormLine && ($candNormLine === $targetNormLine || str_contains($candNormLine, $targetNormLine) || str_contains($targetNormLine, $candNormLine))) {
                    return $candidate;
                }

                if ($candNormStreet && $targetNormStreet && $candNormStreet === $targetNormStreet) {
                    return $candidate;
                }
            }
        }

        return null;
    }

    /**
     * Find or create/update an address for a user.
     */
    public static function findOrCreateOrUpdate(int $userId, array $attributes, bool $isDefault = false): self
    {
        $existing = self::findMatchingForUser($userId, $attributes);

        if ($existing) {
            $updates = [];
            foreach (['name', 'mobile', 'email', 'landmark', 'road', 'state', 'street'] as $field) {
                if (! empty($attributes[$field])) {
                    $updates[$field] = $attributes[$field];
                }
            }
            if ($isDefault) {
                self::where('user_id', $userId)->where('id', '!=', $existing->id)->update(['is_default' => false]);
                $updates['is_default'] = true;
            }
            if (! empty($updates)) {
                $existing->update($updates);
            }

            return $existing;
        }

        if ($isDefault) {
            self::where('user_id', $userId)->update(['is_default' => false]);
        }

        $isFirst = self::where('user_id', $userId)->count() === 0;

        return self::create(array_merge($attributes, [
            'user_id' => $userId,
            'is_default' => $isDefault || $isFirst,
        ]));
    }
}

