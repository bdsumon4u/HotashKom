<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountingEnabled
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('accounting.enabled', true)) {
            abort(404);
        }

        $admin = $request->user('admin') ?? auth('admin')->user();

        if (! $admin || ! $admin->is('admin')) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
