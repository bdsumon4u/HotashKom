<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\GoogleTagManager\GoogleTagManagerFacade;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource on the frontend.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $blogs = Blog::latest()->paginate(9);

        if (GoogleTagManagerFacade::isEnabled()) {
            GoogleTagManagerFacade::set([
                'event' => 'page_view',
                'page_type' => 'blog_index',
                'customer' => customer_info(),
            ]);
        }

        return view('blogs.index', compact('blogs'));
    }

    /**
     * Display the specified resource on the frontend.
     *
     * @return Response
     */
    public function show(Blog $blog)
    {
        if (GoogleTagManagerFacade::isEnabled()) {
            GoogleTagManagerFacade::set([
                'event' => 'page_view',
                'page_type' => 'blog_show',
                'content' => $blog->toArray(),
                'customer' => customer_info(),
            ]);
        }

        return view('blogs.show', compact('blog'));
    }
}
