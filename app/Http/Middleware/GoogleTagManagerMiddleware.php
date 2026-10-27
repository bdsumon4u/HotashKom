<?php

namespace App\Http\Middleware;

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

        // Only cleanup temporary direct checkout carts ('kart', 'landing') when the user
        // genuinely navigates away from checkout via a standard, full-page GET request.
        $isSafeRoute = $request->is([
            'checkout*',
            'reseller/checkout*',
            'save-checkout-progress*',
            'cart*',
            'livewire*',
            'api/*',
            'thank-you*',
            'payment*',
            'bkash*',
            'nagad*',
            'order*',
            'track-order*',
        ]);

        $isBackgroundOrSubrequest = ! $request->isMethod('GET')
            || $request->ajax()
            || $request->expectsJson()
            || $request->prefetch()
            || $request->header('Purpose') === 'prefetch'
            || $request->header('Sec-Purpose') === 'prefetch'
            || $request->hasHeader('X-Livewire')
            || $request->hasHeader('X-Livewire-Navigate');

        if (! $isSafeRoute && ! $isBackgroundOrSubrequest && in_array(session('kart'), ['kart', 'landing'], true)) {
            session(['kart' => 'default']);
        }

        return $next($request);
    }
}
