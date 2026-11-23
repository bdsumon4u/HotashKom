<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\UtmTrackingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptureUtmTracking
{
    public function __construct(
        protected UtmTrackingService $utmTrackingService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isCustomerRequest = ! $request->is('admin', 'admin/*');

        if ($isCustomerRequest) {
            $this->utmTrackingService->captureFromRequest($request);
        }

        if ($request->isMethod('GET')) {
            $ref = $request->query('ref') ?? $request->query('referral_code');
            if (! empty($ref)) {
                session(['ref' => $ref]);
            }
        }

        $response = $next($request);

        if ($isCustomerRequest && $request->isMethod('GET') && ! $request->header('X-Livewire') &&
            str_contains((string) $response->headers->get('Content-Type'), 'text/html') &&
            is_string($response->getContent()) && str_contains($response->getContent(), '</head>')) {
            $script = '<script src="'.e(asset('js/utm-tracking.js')).'" data-cookie-domain="'.e((string) config('session.domain')).'" data-navigate-once></script>';
            $response->setContent(str_replace('</head>', $script.'</head>', $response->getContent()));
            $response->headers->remove('Content-Length');
            $response->headers->remove('ETag');
        }

        return $response;
    }
}
