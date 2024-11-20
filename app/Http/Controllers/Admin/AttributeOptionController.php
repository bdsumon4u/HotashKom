<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Option;
use Illuminate\Http\Request;

class AttributeOptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Attribute $attribute)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Attribute $attribute)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Attribute $attribute)
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');
        if (! $request->has('value')) {
            $request->merge(['value' => $request->name]);
        }

        $attribute->options()->create($request->validate([
            'name' => 'required',
            'value' => 'required',
        ]));

        return back()->withSuccess('Option created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Attribute $attribute, Option $option)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Attribute $attribute, Option $option)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attribute $attribute, Option $option)
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');
        if (! $request->has('value')) {
            $request->merge(['value' => $request->name]);
        }

        $option->update($request->validate([
            'name' => 'required',
            'value' => 'required',
        ]));

        return back()->withSuccess('Option updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attribute $attribute, Option $option)
    {
        abort_unless(request()->user()->is('admin'), 403, 'You don\'t have permission.');
        $option->delete();

        return back()->withSuccess('Option deleted successfully');
    }
}
