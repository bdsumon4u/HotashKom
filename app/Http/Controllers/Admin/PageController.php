<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');

        return $this->view([
            'pages' => Page::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');

        return $this->view();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');
        $data = $request->validate([
            'title' => 'required',
            'slug' => 'required|unique:pages',
            'content' => 'required',
        ]);

        Page::create($data);

        return redirect()->route('admin.pages.index')->withSuccess('Page Created.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Page $page)
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');

        return $this->view();
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Page $page)
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');
        $data = $request->validate([
            'title' => 'required',
            'slug' => 'required|unique:pages,slug,'.$page->id,
            'content' => 'required',
        ]);

        $page->update($data);

        return redirect()->route('admin.pages.index')->withSuccess('Page Updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Page $page)
    {
        abort_unless(request()->user()->is('admin'), 403, 'You don\'t have permission.');
        $page->delete();

        return back()->withSuccess('Page Deleted.');
    }
}
