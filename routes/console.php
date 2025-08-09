<?php

use App\Jobs\CleanupDuplicateRelationships;
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

// Cleanup duplicate relationships command
Artisan::command('resellers:cleanup-duplicates {--product-id= : Clean up duplicates for a specific product ID}', function () {
    $productId = $this->option('product-id');

    $this->info('Starting cleanup of duplicate relationships...');

    if ($productId) {
        $this->info("Cleaning up duplicates for product ID: {$productId}");
        CleanupDuplicateRelationships::dispatch((int) $productId);
    } else {
        $this->info('Cleaning up duplicates for all products...');
        CleanupDuplicateRelationships::dispatch();
    }

    $this->info('Cleanup job dispatched successfully!');
    $this->info('Check the logs for progress and results.');
})->purpose('Clean up duplicate relationships in reseller databases');
