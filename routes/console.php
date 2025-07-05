<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Schedule queue worker to run every minute
Schedule::command('queue:work --timeout=90 --tries=3 --sleep=1 --max-jobs=100 --max-time=300 --daemon --stop-when-empty')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();
