<?php

declare(strict_types=1);

namespace App\Observers;

use Spatie\ResponseCache\Facades\ResponseCache;

final class ResponseCacheObserver
{
    public function created(mixed $model): void
    {
        $this->clear();
    }

    public function updated(mixed $model): void
    {
        $this->clear();
    }

    public function deleted(mixed $model): void
    {
        $this->clear();
    }

    public function restored(mixed $model): void
    {
        $this->clear();
    }

    public function forceDeleted(mixed $model): void
    {
        $this->clear();
    }

    private function clear(): void
    {
        if (! config('cache.response_cache.enabled')) {
            return;
        }

        ResponseCache::clear();
    }
}
