<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;

class ActivityLog extends Model
{
    protected $fillable = [
        'actor_type',
        'actor_id',
        'actor_name',
        'actor_email',
        'action',
        'category',
        'description',
        'subject_type',
        'subject_id',
        'subject_ref',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /**
     * Record a new activity log entry safely.
     */
    public static function record(
        string $action,
        string $description,
        string $category = 'orders',
        ?string $actorType = null,
        ?string $actorName = null,
        ?string $actorEmail = null,
        ?int $actorId = null,
        ?string $subjectType = null,
        ?string $subjectId = null,
        ?string $subjectRef = null,
        ?array $metadata = null
    ): ?self {
        try {
            // Determine actor if not provided
            if ($actorType === null) {
                if (Auth::check()) {
                    $user = Auth::user();
                    $actorType = $user->isAdmin() ? 'admin' : 'customer';
                    $actorId = $user->id;
                    $actorName = $user->name;
                    $actorEmail = $user->email;
                } else {
                    $actorType = 'customer';
                    $actorName = $actorName ?: 'Guest Customer';
                }
            }

            // Also mirror to Laravel system log for persistent disk trail
            Log::info("[Activity: {$category}.{$action}] {$description}", $metadata ?? []);

            // Check if table exists before inserting (prevents crashes before migration)
            if (! Schema::hasTable('activity_logs')) {
                return null;
            }

            return self::create([
                'actor_type' => $actorType,
                'actor_id' => $actorId,
                'actor_name' => $actorName,
                'actor_email' => $actorEmail,
                'action' => strtoupper($action),
                'category' => strtolower($category),
                'description' => $description,
                'subject_type' => $subjectType,
                'subject_id' => $subjectId ? (string) $subjectId : null,
                'subject_ref' => $subjectRef,
                'metadata' => $metadata,
                'ip_address' => Request::ip(),
                'user_agent' => substr((string) Request::userAgent(), 0, 255),
            ]);
        } catch (\Throwable $e) {
            Log::warning("Failed to save ActivityLog entry: " . $e->getMessage());
            return null;
        }
    }

    /* -------------------------------- Scopes -------------------------------- */

    public function scopeCategory($query, ?string $category)
    {
        if ($category && $category !== 'all') {
            return $query->where('category', $category);
        }
        return $query;
    }

    public function scopeActorType($query, ?string $actorType)
    {
        if ($actorType && $actorType !== 'all') {
            return $query->where('actor_type', $actorType);
        }
        return $query;
    }

    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        $term = trim($term);
        return $query->where(function ($q) use ($term) {
            $q->where('description', 'like', "%{$term}%")
              ->orWhere('subject_ref', 'like', "%{$term}%")
              ->orWhere('actor_name', 'like', "%{$term}%")
              ->orWhere('actor_email', 'like', "%{$term}%")
              ->orWhere('action', 'like', "%{$term}%");
        });
    }

    /* --------------------------- UI Helpers --------------------------- */

    public function getBadgeClassAttribute(): string
    {
        return match (true) {
            str_contains($this->action, 'DELETE') => 'bg-rose-100 text-rose-800 border-rose-200',
            str_contains($this->action, 'REJECT') || str_contains($this->action, 'CANCEL') => 'bg-amber-100 text-amber-900 border-amber-300',
            str_contains($this->action, 'RESTORE') => 'bg-emerald-100 text-emerald-900 border-emerald-300',
            str_contains($this->action, 'CONFIRM') || str_contains($this->action, 'PAID') || str_contains($this->action, 'DELIVERED') => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            str_contains($this->action, 'PLACE') || str_contains($this->action, 'CREATED') => 'bg-blue-100 text-blue-800 border-blue-200',
            str_contains($this->action, 'SHIP') => 'bg-indigo-100 text-indigo-800 border-indigo-200',
            default => 'bg-stone-100 text-stone-700 border-stone-200',
        };
    }

    public function getCategoryColorAttribute(): string
    {
        return match ($this->category) {
            'inventory' => 'text-emerald-700 bg-emerald-50 border-emerald-200',
            'orders' => 'text-amber-800 bg-amber-50 border-amber-200',
            'auth' => 'text-blue-700 bg-blue-50 border-blue-200',
            'system' => 'text-purple-700 bg-purple-50 border-purple-200',
            default => 'text-stone-700 bg-stone-50 border-stone-200',
        };
    }
}
