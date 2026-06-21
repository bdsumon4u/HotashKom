<?php

namespace App\Http\CacheProfiles;

use Illuminate\Http\Request;
use Spatie\ResponseCache\CacheProfiles\BaseCacheProfile;

class StorefrontCacheProfile extends BaseCacheProfile
{
    /**
     * Determine if the response cache middleware should be enabled for the given request.
     */
    public function shouldCacheRequest(Request $request): bool
    {
        // Only cache GET requests to storefront routes
        return $request->is('api/storefront/*') && $request->isMethod('GET');
    }

    /**
     * Return the time in seconds the given request should be cached.
     */
    public function cacheLifetime(Request $request): int
    {
        // Cache product detail for 5 minutes (300 seconds)
        // Cache listings for 2 minutes (120 seconds)
        if (str_contains($request->path(), 'products/') && ! str_contains($request->path(), 'related')) {
            return 600;
        }

        if (str_contains($request->path(), 'related') || str_contains($request->path(), 'reviews')) {
            return 600;
        }

        // Default: cache other storefront endpoints for 2 minutes
        return 300;
    }

    /**
     * Determine if the cache should be used for the given request.
     * This prevents caching when the user is logged in (for admin actions).
     */
    public function shouldUseCache(Request $request): bool
    {
        return ! $request->user();
    }
}
