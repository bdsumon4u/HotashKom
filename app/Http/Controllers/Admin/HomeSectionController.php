<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\HomeSectionRequest;
use App\Models\Category;
use App\Models\HomeSection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class HomeSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');
        if (request()->has('orders')) {
            $orders = request('orders');
            DB::statement('UPDATE home_sections SET `order` = CASE id '.implode(' ', array_map(fn ($id): string => "WHEN $id THEN $orders[$id] ", array_keys($orders))).'END');

            // Clear cache after reordering (model events don't fire for direct DB updates)
            cacheMemo()->forget('homesections');
            cacheMemo()->forget('api_sections');
            cacheInvalidateNamespace('api_sections');

            return response()->json(['message' => 'Sections Have Been Reordered.']);
        }

        return $this->view([
            'sections' => HomeSection::orderBy('order', 'asc')->get(),
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

        $view = '';
        if (request('banner')) {
            $view = 'banner-create';
        } elseif (request('content')) {
            $view = 'content-create';
        }

        return $this->view([
            'categories' => Category::nested(),
            'pages' => \App\Models\Page::all(),
        ], $view);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(HomeSectionRequest $request)
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');

        $data = $request->validated();
        $categories = Arr::pull($data, 'categories');

        // Extract SEO data before creating
        $seoData = $request->input('seo', []);
        $seoData = array_filter($seoData, fn ($value): bool => ! empty($value));

        $homeSection = HomeSection::create($data);

        // Handle SEO data
        if (! empty($seoData)) {
            $homeSection->seo()->updateOrCreate([], $seoData);
        }

        if ($categories) {
            $homeSection->categories()->sync($categories);
        }
        // Cache is cleared automatically by model events

        return to_route('admin.home-sections.edit', $homeSection)->with('success', 'Section Has Been Created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(HomeSection $homeSection): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(HomeSection $homeSection)
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');

        $view = '';
        if ($homeSection->type == 'banner') {
            $view = 'banner-edit';
        } elseif ($homeSection->type == 'content') {
            $view = 'content-edit';
        }

        return $this->view([
            'section' => $homeSection->load('seo'),
            'categories' => Category::nested(),
            'pages' => \App\Models\Page::all(),
        ], $view);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(HomeSectionRequest $request, HomeSection $homeSection)
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');
        $data = $request->validated();
        $categories = Arr::pull($data, 'categories');

        // Extract SEO data before updating
        $seoData = $request->input('seo', []);
        $seoData = array_filter($seoData, fn ($value): bool => ! empty($value));

        $homeSection->update($data);

        // Handle SEO data
        if (! empty($seoData)) {
            $homeSection->seo()->updateOrCreate([], $seoData);
        } elseif ($request->has('seo')) {
            // If seo key exists but is empty, delete SEO data
            $homeSection->seo?->delete();
        }

        $homeSection->categories()->sync($categories);
        // Cache is cleared automatically by model events

        return to_route('admin.home-sections.index')->with('success', 'Section Has Been Updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(HomeSection $homeSection)
    {
        abort_unless(request()->user()->is('admin'), 403, 'You don\'t have permission.');
        $homeSection->delete();

        return back()->withSuccess('Section Has Been Deleted.');
    }
}
