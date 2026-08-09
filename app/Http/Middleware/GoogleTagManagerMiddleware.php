<?php

namespace App\Http\Middleware;

use Azmolla\Shoppingcart\Facades\Cart;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            Cart::instance('kart')->destroy();
            Cart::instance('landing')->destroy();

            DB::table('shopping_cart')
                ->where('identifier', session()->getId())
                ->whereIn('instance', ['kart', 'landing'])
                ->delete();

            session(['kart' => 'default']);
        }

        return $next($request);
    }
}
