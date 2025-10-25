<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Redirect;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  array  $param
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, $param = null)
    {
        $guard = null;

        $param = explode(';', $param);
        $guard = $param[0] ?? $guard;
        $redirect = $param[1] ?? "$guard.verification.notice";
        $type = $param[2] ?? 'route';

        if (! $request->user($guard) ||
            ($request->user($guard) instanceof MustVerifyEmail &&
            ! $request->user($guard)->hasVerifiedEmail())) {
            return $request->expectsJson()
                    ? abort(403, 'Your email address is not verified.')
                    : $type == 'url' ? Redirect::to($redirect) : Redirect::route($redirect);
        }

        return $next($request);
    }
}
