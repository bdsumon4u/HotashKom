<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            $upper = strtoupper(str_replace(['_', '-'], '', $guard));

            if ($upper === 'USER') {
                return redirect('/reseller/dashboard');
            }

            return $upper === 'WEB' || $upper == null
                ? redirect('/reseller/dashboard')
                : redirect($guard.'/dashboard');
        }

        // if (Auth::guard($guard)->check()) {
        //     return redirect(RouteServiceProvider::HOME);
        // }

        return $next($request);
    }
}
