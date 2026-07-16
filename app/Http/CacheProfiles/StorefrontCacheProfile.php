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
     * Cache all GET requests to storefront web routes and API storefront endpoints.
     * Skip caching for authenticated users so their session data is never shared.
     */
    public function shouldCacheRequest(Request $request): bool
    {
        if (! $request->isMethod('GET')) {
            return false;
        }

        // Never cache requests for authenticated users
        if (auth()->check()) {
            return false;
        }

        // Cache API storefront endpoints
        if ($request->is('api/storefront/*')) {
            return true;
        }

        // Cache key customer-facing web pages for guests only
        return $request->is('/')
            || $request->is('products/*')
            || $request->is('shop')
            || $request->is('category/*')
            || $request->is('categories/*')
            || $request->is('brand/*')
            || $request->is('brands/*')
            || $request->is('blogs')
            || $request->is('blogs/*')
            || $request->is('sections/*');
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
        // Cache homepage and section listing pages for 30 minutes
        if ($request->is('/') || $request->is('sections/*')) {
            return 1800;
        }

        // Cache product detail for 15 minutes
        if ($request->is('products/*')) {
            return 900;
        }

        // Cache category/brand listing pages for 20 minutes
        if ($request->is('category/*') || $request->is('categories/*')
            || $request->is('brand/*') || $request->is('brands/*')) {
            return 1200;
        }

        // Default: 5 minutes for API and other storefront endpoints
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
