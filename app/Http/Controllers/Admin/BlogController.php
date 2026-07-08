<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Traits\ImageUploader;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BlogController extends Controller
{
    use ImageUploader;

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        abort_if(request()->user()->is(['salesman', 'uploader']), 403, 'You don\'t have permission.');

        return $this->view([
            'blogs' => Blog::latest()->get(),
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
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'regex:/^[a-zA-Z0-9-]+$/', 'unique:blogs'],
            'content' => ['required'],
            'image' => ['nullable', 'image', 'max:2048'],
        ], [
            'slug.regex' => 'The link field may only contain letters, numbers, and hyphens. No spaces or special characters are allowed.',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'), [
                'dir' => 'blogs',
                'width' => 800,
                'height' => 450,
                'resize' => true,
                'method' => 'resize',
            ]);
        }

        Blog::create($data);

        return to_route('admin.blogs.index')->withSuccess('Blog Created.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(Blog $blog)
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');

        return $this->view(compact('blog'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, Blog $blog)
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'regex:/^[a-zA-Z0-9-]+$/', 'unique:blogs,slug,'.$blog->id],
            'content' => ['required'],
            'image' => ['nullable', 'image', 'max:2048'],
        ], [
            'slug.regex' => 'The link field may only contain letters, numbers, and hyphens. No spaces or special characters are allowed.',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'), [
                'dir' => 'blogs',
                'width' => 800,
                'height' => 450,
                'resize' => true,
                'method' => 'resize',
            ]);
        }

        $blog->update($data);

        return to_route('admin.blogs.index')->withSuccess('Blog Updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(Blog $blog)
    {
        abort_unless(request()->user()->is('admin'), 403, 'You don\'t have permission.');

        $blog->delete();

        return back()->withSuccess('Blog Deleted.');
    }
}
