<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Traits\PreventsSourcedResourceDeletion;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class BrandController extends Controller
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
            'brands' => Brand::cached(),
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
        $data = $request->validate([
            'name' => ['required', 'unique:brands'],
            'slug' => ['required', 'unique:brands'],
            'base_image' => ['nullable', 'integer'],
            'is_enabled' => ['boolean'],
        ]);

        $data['image_id'] = Arr::pull($data, 'base_image');

        Brand::create($data);

        return back()->with('success', 'Brand Has Been Created.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Brand $brand)
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');
        $data = $request->validate([
            'name' => 'required|unique:brands,name,'.$brand->id,
            'slug' => 'required|unique:brands,slug,'.$brand->id,
            'base_image' => ['nullable', 'integer'],
            'is_enabled' => ['boolean'],
        ]);

        $data['image_id'] = Arr::pull($data, 'base_image');

        $brand->update($data);

        return back()->with('success', 'Brand Has Been Updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Brand $brand)
    {
        abort_unless(request()->user()->is('admin'), 403, 'You don\'t have permission.');

        if (($result = $this->preventSourcedResourceDeletion($brand)) !== true) {
            return $result;
        }

        $brand->delete();

        return redirect()
            ->action([static::class, 'index'])
            ->with('success', 'Brand Has Been Deleted.');
    }
}
