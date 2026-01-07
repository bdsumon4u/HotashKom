<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Traits\PreventsSourcedResourceDeletion;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    use PreventsSourcedResourceDeletion;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');

        return $this->view([
            'attributes' => Attribute::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): void
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');
        Attribute::create($request->validate([
            'name' => ['required'],
        ]));

        return redirect()->action(static::index(...));
    }

    /**
     * Display the specified resource.
     */
    public function show(Attribute $attribute): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attribute $attribute): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Attribute $attribute)
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');
        $attribute->update($request->validate([
            'name' => ['required'],
        ]));

        return redirect()->action(static::index(...));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attribute $attribute)
    {
        abort_unless(request()->user()->is('admin'), 403, 'You don\'t have permission.');

        if (($result = $this->preventSourcedResourceDeletion($attribute)) !== true) {
            return $result;
        }

        $attribute->delete();

        return back()->with('success', 'Attribute Has Been Deleted.');
    }
}
