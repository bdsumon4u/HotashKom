<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slide;
use App\Traits\ImageUploader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SlideController extends Controller
{
    use ImageUploader;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(request()->user()->is(['salesman', 'uploader']), 403, 'You don\'t have permission.');

        return $this->view([
            'slides' => Slide::all(),
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
        $request->validate([
            'file' => ['required', 'image'],
        ]);

        $file = $request->file('file');

        return Slide::create([
            'is_active' => true,
            'object_fit' => config('services.slides.object_fit', 'cover'),
            'mobile_src' => $this->uploadImage($file, [
                'width' => config('services.slides.mobile.width', 360),
                'height' => config('services.slides.mobile.height', 180),
                'resize' => config('services.slides.mobile.resize', true),
                'method' => config('services.slides.mobile.method', 'resize'),
                'dir' => 'slides/mobile',
            ]),
            'desktop_src' => $this->uploadImage($file, [
                'width' => config('services.slides.desktop.width', 1125),
                'height' => config('services.slides.desktop.height', 395),
                'resize' => config('services.slides.desktop.resize', true),
                'method' => config('services.slides.desktop.method', 'resize'),
                'dir' => 'slides/desktop',
            ]),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Slide $slide): void
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Slide $slide)
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');

        return $this->view(compact('slide'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Slide $slide)
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');
        $data = $request->validate([
            'title' => ['nullable', 'max:255'],
            'text' => ['nullable', 'max:255'],
            'btn_name' => ['nullable', 'max:20'],
            'btn_href' => ['nullable', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'object_fit' => ['nullable', 'string', 'in:contain,cover,fill,none,scale-down,inherit,initial,revert,revert-layer,unset'],
        ]);

        $slide->update($data);

        return back()->with('success', 'Slide Has Been Updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Slide $slide)
    {
        abort_unless(request()->user()->is('admin'), 403, 'You don\'t have permission.');
        Storage::disk('public')->delete(Str::after($slide->mobile_src, 'storage'));
        Storage::disk('public')->delete(Str::after($slide->desktop_src, 'storage'));
        $slide->delete();

        return back()->with('success', 'Slide Has Been Deleted.');
    }
}
