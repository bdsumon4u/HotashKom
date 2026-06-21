<?php

namespace App\Http\CacheProfiles;

use DateTime;
use Illuminate\Http\Request;
use Spatie\ResponseCache\CacheProfiles\BaseCacheProfile;
use Symfony\Component\HttpFoundation\Response;

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
     * Determine if the response should be cached based on its status code.
     * Only successful responses should be cached.
     */
    public function shouldCacheResponse(Response $response): bool
    {
        return $response->isSuccessful();
    }

    /**
     * Return the time in seconds the given request should be cached.
     */
    public function cacheLifetime(Request $request): int
    {
        // Cache product detail for 10 minutes
        if (str_contains($request->path(), 'products/') && ! str_contains($request->path(), 'related')) {
            return 600;
        }

        // Cache related products and reviews for 10 minutes
        if (str_contains($request->path(), 'related') || str_contains($request->path(), 'reviews')) {
            return 600;
        }

        // Default: cache other storefront endpoints for 5 minutes
        return 300;
    }

    /**
     * Override cacheRequestUntil to use our dynamic cacheLifetime per-request.
     */
    public function cacheRequestUntil(Request $request): DateTime
    {
        return now()->addSeconds($this->cacheLifetime($request));
    }
}
