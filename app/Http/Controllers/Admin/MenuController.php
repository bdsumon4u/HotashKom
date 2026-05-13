<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');

        return $this->view([
            'menus' => Menu::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');

        return $this->view();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');
        $data = $request->validate([
            'name' => ['required'],
            'slug' => ['required', 'regex:/^[a-zA-Z0-9-]+$/', 'unique:menus'],
        ], [
            'slug.regex' => 'The link field may only contain letters, numbers, and hyphens. No spaces or special characters are allowed.',
        ]);

        return to_route('admin.menus.edit', Menu::create($data))->withSuccess('Menu Has Been Created.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(Menu $menu)
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');

        return $this->view();
    }
}
