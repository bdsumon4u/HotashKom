<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class CacheStaticAssets
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only cache GET requests
        if ($request->method() !== 'GET') {
            return $response;
        }

        $path = $request->path();
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        // Define cache durations for different asset types
        $cacheDurations = [
            'css' => 31536000,   // 1 year
            'js' => 31536000,    // 1 year
            'jpg' => 31536000,   // 1 year
            'jpeg' => 31536000,  // 1 year
            'png' => 31536000,   // 1 year
            'gif' => 31536000,   // 1 year
            'webp' => 31536000,  // 1 year
            'svg' => 31536000,   // 1 year
            'ico' => 31536000,   // 1 year
            'woff' => 31536000,  // 1 year
            'woff2' => 31536000, // 1 year
            'ttf' => 31536000,   // 1 year
            'eot' => 31536000,   // 1 year
            'otf' => 31536000,   // 1 year
        ];

        // Check if this is a static asset
        if (isset($cacheDurations[$extension])) {
            $maxAge = $cacheDurations[$extension];

            // Set cache headers
            $response->headers->set('Cache-Control', "public, max-age={$maxAge}, immutable");
            $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + $maxAge).' GMT');
            $response->headers->set('Last-Modified', gmdate('D, d M Y H:i:s', time()).' GMT');

            // Add ETag for better cache validation
            $etag = md5($response->getContent());
            $response->headers->set('ETag', "\"{$etag}\"");

            // Handle If-None-Match header (304 Not Modified)
            if ($request->headers->get('If-None-Match') === "\"{$etag}\"") {
                return response('', 304, $response->headers->all());
            }
        }

        return $response;
    }
}
