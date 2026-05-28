<?php

namespace App\Http\Middleware;

use Azmolla\Shoppingcart\Facades\Cart;
use Closure;
use Illuminate\Http\Request;
use Spatie\GoogleTagManager\GoogleTagManagerFacade;

class GoogleTagManagerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($gtmId = setting('gtm_id')) {
            config(['googletagmanager.id' => $gtmId]);
            GoogleTagManagerFacade::setId(config('googletagmanager.id'));
            GoogleTagManagerFacade::enable();
        } else {
            GoogleTagManagerFacade::disable();
        }

        if (! $request->is('checkout') && ! $request->is('save-checkout-progress')) {
            if (config('app.order_now_is_onetime')) {
                Cart::instance('kart')->destroy();
            }
            Cart::instance('landing')->destroy();
            session(['kart' => 'default']);
        }

        return $next($request);
    }
}
