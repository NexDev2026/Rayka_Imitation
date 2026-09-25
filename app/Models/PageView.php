<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    protected $fillable = [
        'ip_address',
        'url',
        'user_agent',
        'viewed_date',
    ];

    protected $casts = [
        'viewed_date' => 'date',
    ];

    /**
     * Scope query to only authentic real visitors (excluding dummy seeder IPs and local loopbacks)
     */
    public function scopeAuthentic($query)
    {
        return $query->whereNotIn('ip_address', ['127.0.0.1', '::1', 'localhost'])
            ->where('ip_address', 'not like', '192.168.%')
            ->where('ip_address', 'not like', '10.%')
            ->where('ip_address', 'not like', '172.16.%');
    }
}
