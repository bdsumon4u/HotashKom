<?php

declare(strict_types=1);

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\ResponseCache\Facades\ResponseCache;

uses(RefreshDatabase::class);

it('clears response cache when products change', function (): void {
    config()->set('cache.response_cache.enabled', true);

    ResponseCache::shouldReceive('clear')->atLeast()->once();

    Product::factory()->create();
});

it('clears response cache when brands change', function (): void {
    config()->set('cache.response_cache.enabled', true);

    ResponseCache::shouldReceive('clear')->atLeast()->once();

    Brand::factory()->create();
});

it('does not clear response cache when disabled', function (): void {
    config()->set('cache.response_cache.enabled', false);

    ResponseCache::shouldReceive('clear')->never();

    Product::factory()->create();
});
