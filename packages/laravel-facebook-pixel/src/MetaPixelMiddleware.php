<?php

namespace Hotash\FacebookPixel;

use Closure;
use Illuminate\Session\Store as Session;
use Illuminate\Support\Facades\Cookie;

class MetaPixelMiddleware
{
    protected MetaPixel $facebookPixel;

    protected Session $session;

    public function __construct(MetaPixel $facebookPixel, Session $session)
    {
        $this->facebookPixel = $facebookPixel;
        $this->session = $session;
    }

    public function handle($request, Closure $next)
    {
        // Handle fbclid parameter
        // if ($fbclid = $request->get('fbclid')) {
        //     Cookie::queue('_fbc', $fbclid, 90 * 24 * 60); // 90 days
        // }

        if ($this->session->has($this->facebookPixel->sessionKey())) {
            $this->facebookPixel->merge($this->session->get($this->facebookPixel->sessionKey()));
        }
        $response = $next($request);

        $this->session->flash($this->facebookPixel->sessionKey(), $this->facebookPixel->getFlashedEvent());

        return $response;
    }
}
