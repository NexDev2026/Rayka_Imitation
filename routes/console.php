<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

// Prune empty carts daily
Schedule::command('carts:prune-empty')->daily();

// Automated Database Backup: Runs twice daily at 14:00 (2:00 PM IST) and 02:00 (2:00 AM IST)
Schedule::command('db:backup')
    ->timezone('Asia/Kolkata')
    ->dailyAt('14:00')
    ->runInBackground();

Schedule::command('db:backup')
    ->timezone('Asia/Kolkata')
    ->dailyAt('02:00')
    ->runInBackground();
