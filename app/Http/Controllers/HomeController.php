<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\GoogleTagManager\GoogleTagManagerFacade;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {

        GoogleTagManagerFacade::set([
            'event' => 'page_view',
            'page_type' => 'home',
        ]);
        //  \LaravelFacebookPixel::createEvent('PageView', $parameters = []);

        return view('index');
    }
}
