<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\GoogleTagManager\GoogleTagManagerFacade;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        abort_if(isOninda() && ! config('app.resell'), 403);

        if (GoogleTagManagerFacade::isEnabled()) {
            GoogleTagManagerFacade::set([
                'event' => 'page_view',
                'page_type' => 'home',
                'customer' => customer_info(),
            ]);
        }

        $ref = $request->query('ref') ?? $request->query('referral_code');
        if (! empty($ref)) {
            session(['ref' => $ref]);
        }

        return view('index');
    }
}
