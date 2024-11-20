<?php

namespace App\Http\Middleware;

use Closure;
use Spatie\GoogleTagManager\GoogleTagManagerFacade;

class GoogleTagManagerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        config(['googletagmanager.id' => setting('gtm_id', 'gtm_id')]);
        GoogleTagManagerFacade::setId(config('googletagmanager.id'));

        if (! $request->is('checkout')) {
            $cart = session()->get('cart', []);
            $kart = session()->get('kart');
            if (isset($cart[$kart])) {
                unset($cart[$kart]);
            }
            session()->put('cart', $cart);
        }

        return $next($request);
    }
}
