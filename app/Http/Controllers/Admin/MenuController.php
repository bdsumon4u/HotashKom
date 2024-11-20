<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
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
            'menus' => Menu::all(),
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
            'name' => 'required',
            'slug' => 'required|unique:menus',
        ]);

        return redirect()->route('admin.menus.edit', Menu::create($data))->withSuccess('Menu Has Been Created.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Menu $menu)
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');

        return $this->view();
    }
}
