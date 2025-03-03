<?php

namespace App\Http\Middleware;

use Azmolla\Shoppingcart\Facades\Cart;
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

        if (! $request->is('checkout') && ! $request->is('save-checkout-progress')) {
            Cart::instance('kart')->destroy();
            Cart::instance('landing')->destroy();
            session(['kart' => 'default']);
        }

        return $next($request);
    }
}
