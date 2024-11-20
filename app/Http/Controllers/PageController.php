<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Spatie\GoogleTagManager\GoogleTagManagerFacade;

class PageController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Page $page)
    {
        GoogleTagManagerFacade::set([
            'event' => 'page_view',
            'page_type' => 'page',
            'content' => $page->toArray(),
        ]);

        return view('page');
    }
}
