<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\View;

class ShortKodeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        if (! method_exists($response, 'content')) {
            return $response;
        }
        $content = $response->content();

        $stack = [
            '[CONTACT_PAGE]' => 'contact-page',
            '[CONTACT_FORM]' => 'contact-form',
            '[FAQs]' => 'faqs',
            '<!-- pagebreak -->' => 'pagebreak',
        ];

        foreach ($stack as $code => $view) {
            if (str_contains((string) $content, $code)) {
                $content = str_replace($code, View::make($view), $content);
            }
        }

        $content = $response->setContent($content);

        return $response;
    }
}
