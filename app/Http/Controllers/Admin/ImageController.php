<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Traits\ImageUploader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
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
        $request->validate([
            'file' => 'required|image',
        ]);

        $file = $request->file('file');

        return Image::create([
            'disk' => 'public',
            'filename' => $file->getClientOriginalName(),
            'path' => $this->uploadImage($file, [
                'width' => 700,
                'height' => 700,
                'dir' => 'images',
                'resize' => $request->has('resize') ? $request->resize : true,
            ]),
            'extension' => $file->guessClientExtension(),
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);
    }

    public function update(Request $request, Image $image)
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');
        $request->validate([
            'filename' => 'required|string',
        ]);

        $image->update($request->only('filename'));

        return request()->expectsJson()
            ? response()->json(['success' => 'Image Has Been Updated.'])
            : back()->with('success', 'Image Has Been Updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Image $image)
    {
        abort_unless(request()->user()->is('admin'), 403, 'You don\'t have permission.');
        if ($image->products->isNotEmpty()) {
            return request()->expectsJson()
                ? response()->json(['danger' => 'Image Is Used.'])
                : back()->with('danger', 'Image Is Used.');
        }

        // $this->delete();
        $image->delete() && Storage::disk($image->disk)->delete(Str::after($image->path, 'storage'));

        return request()->expectsJson()
            ? response()->json(['success' => 'Image Has Been Deleted.'])
            : redirect()
                ->action([self::class, 'index'])
                ->with('success', 'Image Has Been Deleted.');
    }
}
