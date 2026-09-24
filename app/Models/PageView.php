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
}
