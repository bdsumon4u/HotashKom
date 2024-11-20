<?php

namespace App\Http\Controllers;

use App\Models\HomeSection;
use App\Models\Slide;
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
        $slides = cache()->rememberForever('slides', function () {
            return Slide::whereIsActive(1)->get();
        });
        $sections = cache()->rememberForever('homesections', function () {
            return HomeSection::orderBy('order', 'asc')->get();
        });

        return view('index', compact('slides', 'sections'));
    }
}
