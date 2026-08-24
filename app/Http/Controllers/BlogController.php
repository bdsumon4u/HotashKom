<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Support\BlogTableOfContents;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\GoogleTagManager\GoogleTagManagerFacade;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource on the frontend.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $blogs = Blog::latest()->paginate(9);

        if (GoogleTagManagerFacade::isEnabled()) {
            GoogleTagManagerFacade::set([
                'event' => 'page_view',
                'page_type' => 'blog_index',
                'customer' => customer_info(),
            ]);
        }

        return view('blogs.index', compact('blogs'));
    }

    /**
     * Display the specified resource on the frontend.
     *
     * @return Response
     */
    public function show(
        Blog $blog,
        BlogTableOfContents $tableOfContentsBuilder
    ) {
        if (GoogleTagManagerFacade::isEnabled()) {
            GoogleTagManagerFacade::set([
                'event' => 'page_view',
                'page_type' => 'blog_show',
                'content' => $blog->toArray(),
                'customer' => customer_info(),
            ]);
        }

        $processedContent = $tableOfContentsBuilder->build(
            $blog->content
        );

        /*
         * Blog internal links builder
         *
         * Server-rendered blog internal linking.
         * Uses Laravel Collection operations only.
         * No SQL ID subtraction or ABS() arithmetic.
         */

        $blogLinkPool = Blog::query()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get([
                'id',
                'title',
                'slug',
                'created_at',
            ]);

        $currentBlogIndex = $blogLinkPool->search(
            function ($item) use ($blog) {
                return (int) $item->getKey()
                    === (int) $blog->getKey();
            }
        );

        $previousBlog = null;
        $nextBlog = null;
        $relatedBlogs = collect();

        if (
            $currentBlogIndex !== false
            && $blogLinkPool->count() > 1
        ) {
            $totalBlogs = $blogLinkPool->count();

            if ($currentBlogIndex > 0) {
                $previousBlog = $blogLinkPool->get(
                    $currentBlogIndex - 1
                );
            }

            if ($currentBlogIndex < ($totalBlogs - 1)) {
                $nextBlog = $blogLinkPool->get(
                    $currentBlogIndex + 1
                );
            }

            $excludedIds = [
                (int) $blog->getKey(),
            ];

            if ($previousBlog) {
                $excludedIds[] =
                    (int) $previousBlog->getKey();
            }

            if ($nextBlog) {
                $excludedIds[] =
                    (int) $nextBlog->getKey();
            }

            $selectedIds = [];

            /*
             * Circular traversal prevents older articles
             * from becoming isolated as new blogs are added.
             */
            for (
                $offset = 1;
                $offset < $totalBlogs
                    && $relatedBlogs->count() < 3;
                $offset++
            ) {
                $candidateIndex =
                    ($currentBlogIndex + $offset)
                    % $totalBlogs;

                $candidate =
                    $blogLinkPool->get($candidateIndex);

                if (! $candidate) {
                    continue;
                }

                $candidateId =
                    (int) $candidate->getKey();

                if (
                    in_array(
                        $candidateId,
                        $excludedIds,
                        true
                    )
                ) {
                    continue;
                }

                if (isset($selectedIds[$candidateId])) {
                    continue;
                }

                $relatedBlogs->push($candidate);

                $selectedIds[$candidateId] = true;
            }
        }

        return view('blogs.show', [
            'blog' => $blog,
            'blogContent' => $processedContent['content'],
            'tableOfContents' => $processedContent['items'],
            'previousBlog' => $previousBlog,
            'nextBlog' => $nextBlog,
            'relatedBlogs' => $relatedBlogs,
        ]);
    }
}
