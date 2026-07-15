@extends('layouts.yellow.master')

@section('seo_tags')
    {!! seo()->for($blog) !!}
@endsection

@section('title', $blog->title)

@section('content')

<div class="block">
    <div class="container">
        <!-- Custom CSS for blog article readability -->
        <style>
            .blog-post {
                max-width: 800px;
                margin: 0 auto;
                background: #fff;
                padding: 1rem;
                border-radius: 8px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            }
            .blog-post__header {
                margin-bottom: 2rem;
                text-align: center;
            }
            .blog-post__meta {
                font-size: 0.9rem;
                color: #888;
                margin-bottom: 1rem;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
            }
            .blog-post__title {
                font-size: 2.25rem;
                font-weight: 800;
                line-height: 1.3;
                color: #222;
            }
            .blog-post__image-wrapper {
                margin-bottom: 2.5rem;
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            }
            .blog-post__image {
                width: 100%;
                max-height: 480px;
                object-fit: cover;
            }
            .blog-post__content {
                font-size: 1.1rem;
                line-height: 1.8;
                color: #444;
            }
            .blog-post__content p {
                margin-bottom: 1.5rem;
            }
            .blog-post__content img {
                max-width: 100%;
                height: auto;
                border-radius: 6px;
                margin: 1.5rem 0;
            }
            .blog-post__footer {
                margin-top: 3rem;
                padding-top: 2rem;
                border-top: 1px solid #eee;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .blog-post__back-btn {
                font-weight: 600;
                color: #333;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                transition: color 0.2s ease;
            }
            .blog-post__back-btn:hover {
                color: #ffd333;
            }
        </style>

        <article class="blog-post">
            <header class="blog-post__header">
                <div class="blog-post__meta">
                    <i class="far fa-calendar-alt"></i>
                    <span>Published on {{ $blog->created_at->format('M d, Y') }}</span>
                </div>
                <h1 class="blog-post__title">{{ $blog->title }}</h1>
            </header>

            @if($blog->image)
                <div class="blog-post__image-wrapper">
                    <img src="{{ $blog->image }}" alt="{{ $blog->title }}" class="blog-post__image">
                </div>
            @endif

            <div class="blog-post__content mce-content-body">
                {!! $blog->content !!}
            </div>
        </article>
    </div>
</div>
@endsection
