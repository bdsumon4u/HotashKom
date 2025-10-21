<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureResellerIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! isOninda() || auth('user')->guest()) {
            return $next($request);
        }

        if (! auth('user')->user()->is_verified) {
            return to_route('user.profile');
        }

        return $next($request);
    }
}
