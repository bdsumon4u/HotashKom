<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureSpaResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // If this is a Livewire Navigate request or AJAX request, ensure proper headers
        $isLivewireRequest = $request->header('X-Livewire') !== null;
        $isAjaxRequest = $request->header('X-Requested-With') === 'XMLHttpRequest';
        $isLivewireNavigate = $request->header('X-Livewire-Navigate') !== null ||
                              $request->header('X-Inertia') !== null;

        if ($isLivewireRequest || $isAjaxRequest || $isLivewireNavigate) {
            // Set headers to prevent Cloudflare from caching or timing out
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');

            // Ensure connection is kept alive
            $response->headers->set('Connection', 'keep-alive');

            // Add X-Accel-Buffering header to disable buffering (helps with streaming)
            $response->headers->set('X-Accel-Buffering', 'no');
        }

        // For all GET requests (potential SPA navigation), add timeout protection headers
        if ($request->isMethod('GET') && ! $request->expectsJson()) {
            // Ensure response is sent quickly to prevent Cloudflare timeouts
            $response->headers->set('X-Content-Type-Options', 'nosniff');
        }

        // Enable text compression for text-based responses
        if ($response->headers->get('Content-Type') &&
            (str_contains($response->headers->get('Content-Type'), 'text/html') ||
             str_contains($response->headers->get('Content-Type'), 'text/css') ||
             str_contains($response->headers->get('Content-Type'), 'application/javascript') ||
             str_contains($response->headers->get('Content-Type'), 'application/json'))) {
            // Set Vary header for compression
            $response->headers->set('Vary', 'Accept-Encoding');
        }

        return $response;
    }
}
