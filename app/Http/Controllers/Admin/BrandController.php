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
            'slug' => ['required', 'regex:/^[a-zA-Z0-9-]+$/', 'unique:brands'],
            'base_image' => ['nullable', 'integer'],
            'is_enabled' => ['boolean'],
            'seo.title' => ['nullable', 'string', 'max:255'],
            'seo.description' => ['nullable', 'string', 'max:500'],
            'seo.image' => ['nullable', 'url', 'max:500'],
        ], [
            'slug.regex' => 'The link field may only contain letters, numbers, and hyphens. No spaces or special characters are allowed.',
        ]);

        $data['image_id'] = Arr::pull($data, 'base_image');

        // Extract SEO data before creating
        $seoData = $request->input('seo', []);
        $seoData = array_filter($seoData, fn ($value): bool => ! empty($value));

        $brand = Brand::create($data);

        // Handle SEO data
        if (! empty($seoData)) {
            $brand->seo()->updateOrCreate([], $seoData);
        }

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
            'slug' => ['required', 'regex:/^[a-zA-Z0-9-]+$/', 'unique:brands,slug,'.$brand->id],
            'base_image' => ['nullable', 'integer'],
            'is_enabled' => ['boolean'],
            'seo.title' => ['nullable', 'string', 'max:255'],
            'seo.description' => ['nullable', 'string', 'max:500'],
            'seo.image' => ['nullable', 'url', 'max:500'],
        ], [
            'slug.regex' => 'The link field may only contain letters, numbers, and hyphens. No spaces or special characters are allowed.',
        ]);

        $data['image_id'] = Arr::pull($data, 'base_image');

        // Extract SEO data before updating
        $seoData = $request->input('seo', []);
        $seoData = array_filter($seoData, fn ($value): bool => ! empty($value));

        $brand->update($data);

        // Handle SEO data
        if (! empty($seoData)) {
            $brand->seo()->updateOrCreate([], $seoData);
        } elseif ($request->has('seo')) {
            // If seo key exists but is empty, delete SEO data
            $brand->seo?->delete();
        }

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
