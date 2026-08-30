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
        if ($request->isMethod('GET')) {
            $this->utmTrackingService->captureFromRequest($request);
        }

        return $next($request);
    }
}
