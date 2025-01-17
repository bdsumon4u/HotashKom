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
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');

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
            'file' => 'required|image',
        ]);

        $file = $request->file('file');

        return Slide::create([
            'is_active' => true,
            'mobile_src' => $this->uploadImage($file, [
                'width' => config('services.slides.mobile.0', 360),
                'height' => config('services.slides.mobile.1', 180),
                'dir' => 'slides/mobile',
            ]),
            'desktop_src' => $this->uploadImage($file, [
                'width' => config('services.slides.desktop.0', 1125),
                'height' => config('services.slides.desktop.1', 395),
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
            'title' => 'nullable|max:255',
            'text' => 'nullable|max:255',
            'btn_name' => 'nullable|max:20',
            'btn_href' => 'nullable|max:255',
            'is_active' => 'sometimes|boolean',
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
