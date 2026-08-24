<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Image as MediaImage;
use App\Services\IndexNowService;
use App\Traits\ImageUploader;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Spatie\ResponseCache\Facades\ResponseCache;

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
            'faqs' => ['nullable', 'array', 'max:20'],
            'faqs.*.question' => ['nullable', 'string', 'max:500', 'required_with:faqs.*.answer'],
            'faqs.*.answer' => ['nullable', 'string', 'max:5000', 'required_with:faqs.*.question'],
            'seo.title' => ['nullable', 'string', 'max:255'],
            'seo.description' => ['nullable', 'string', 'max:500'],
            'seo.image' => ['nullable', 'url', 'max:500'],
        ], [
            'slug.regex' => 'The link field may only contain letters, numbers, and hyphens. No spaces or special characters are allowed.',
        ]);

        $data['faqs'] = collect($data['faqs'] ?? [])
            ->map(function (array $faq): array {
                return [
                    'question' => trim((string) ($faq['question'] ?? '')),
                    'answer' => trim((string) ($faq['answer'] ?? '')),
                ];
            })
            ->filter(fn (array $faq): bool => $faq['question'] !== '' && $faq['answer'] !== '')
            ->values()
            ->all();
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'), [
                'dir' => 'blogs',
                'width' => 800,
                'height' => 450,
                'resize' => true,
                'method' => 'resize',
            ]);
        }

        $blog = Blog::create($data);

        $seoData = $request->input('seo', []);
        $seoData = array_filter($seoData, fn ($value): bool => ! empty($value));
        if (! empty($seoData)) {
            $blog->seo()->updateOrCreate([], $seoData);
        }

        if (config('responsecache.enabled', false)) {
            ResponseCache::clear();
        }

        if ($blog->slug) {
            app(IndexNowService::class)->submit(
                route('blogs.show', $blog)
            );
        }

        return to_route('admin.blogs.index')->withSuccess('Blog Created.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */

    /**
     * Upload an image used inside a blog article and return its public URL.
     */
    public function uploadInlineImage(Request $request)
    {
        abort_if(request()->user()->is('salesman'), 403, 'You don\'t have permission.');

        $data = $request->validate([
            'file' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'alt_text' => ['required', 'string', 'max:255'],
        ]);

        $file = $data['file'];

        $path = $this->uploadImage($file, [
            'dir' => 'blogs/inline',
            'resize' => false,
        ]);

        MediaImage::create([
            'filename' => $file->getClientOriginalName(),
            'alt_text' => trim($data['alt_text']),
            'disk' => 'public',
            'path' => $path,
            'extension' => $file->guessClientExtension(),
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        return response()->json([
            'url' => url(Storage::disk('public')->url($path)),
        ]);
    }

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

        $oldSlug = (string) $blog->slug;

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'regex:/^[a-zA-Z0-9-]+$/', 'unique:blogs,slug,'.$blog->id],
            'content' => ['required'],
            'image' => ['nullable', 'image', 'max:2048'],
            'faqs' => ['nullable', 'array', 'max:20'],
            'faqs.*.question' => ['nullable', 'string', 'max:500', 'required_with:faqs.*.answer'],
            'faqs.*.answer' => ['nullable', 'string', 'max:5000', 'required_with:faqs.*.question'],
            'seo.title' => ['nullable', 'string', 'max:255'],
            'seo.description' => ['nullable', 'string', 'max:500'],
            'seo.image' => ['nullable', 'url', 'max:500'],
        ], [
            'slug.regex' => 'The link field may only contain letters, numbers, and hyphens. No spaces or special characters are allowed.',
        ]);

        $data['faqs'] = collect($data['faqs'] ?? [])
            ->map(function (array $faq): array {
                return [
                    'question' => trim((string) ($faq['question'] ?? '')),
                    'answer' => trim((string) ($faq['answer'] ?? '')),
                ];
            })
            ->filter(fn (array $faq): bool => $faq['question'] !== '' && $faq['answer'] !== '')
            ->values()
            ->all();

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

        $seoData = $request->input('seo', []);
        $seoData = array_filter($seoData, fn ($value): bool => ! empty($value));
        if (! empty($seoData)) {
            $blog->seo()->updateOrCreate([], $seoData);
        } elseif ($request->has('seo')) {
            $blog->seo?->delete();
        }

        if (config('responsecache.enabled', false)) {
            ResponseCache::clear();
        }

        if ($blog->slug) {
            $indexNowUrls = [
                route('blogs.show', $blog),
            ];

            if ($oldSlug !== '' && $oldSlug !== (string) $blog->slug) {
                $indexNowUrls[] = route('blogs.show', [
                    'blog' => $oldSlug,
                ]);
            }

            app(IndexNowService::class)->submit($indexNowUrls);
        }

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

        $deletedUrl = $blog->slug
            ? route('blogs.show', ['blog' => $blog->slug])
            : null;

        $blog->delete();

        if ($deletedUrl) {
            app(IndexNowService::class)->submit($deletedUrl);
        }

        if (config('responsecache.enabled', false)) {
            ResponseCache::clear();
        }

        return back()->withSuccess('Blog Deleted.');
    }
}
