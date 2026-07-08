@extends('layouts.yellow.master')

@section('title', 'Blogs & Articles')

@section('content')

@include('partials.page-header', [
    'paths' => [
        url('/') => 'Home',
    ],
    'active' => 'Blogs',
    'page_title' => 'Blogs & Articles'
])

<div class="block">
    <div class="container">
        @if($blogs->isEmpty())
            <div class="text-center py-5">
                <i class="fa fa-book-open fa-3x text-muted mb-3" style="font-size: 4rem; opacity: 0.3;"></i>
                <h3 class="text-muted">No blog posts available.</h3>
                <p>Check back later for exciting new articles!</p>
                <a href="{{ url('/') }}" class="btn btn-primary mt-3">Back to Home</a>
            </div>
        @else
            <!-- Custom CSS block for premium design transitions and hover effects -->
            <style>
                .blog-card {
                    border: none;
                    border-radius: 8px;
                    overflow: hidden;
                    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                    background: #fff;
                    display: flex;
                    flex-direction: column;
                    height: 100%;
                }
                .blog-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
                }
                .blog-card__image-wrapper {
                    position: relative;
                    padding-top: 56.25%; /* 16:9 Aspect Ratio */
                    overflow: hidden;
                    background: #f8f9fa;
                }
                .blog-card__image {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    transition: transform 0.5s ease;
                }
                .blog-card:hover .blog-card__image {
                    transform: scale(1.05);
                }
                .blog-card__body {
                    padding: 1.5rem;
                    display: flex;
                    flex-direction: column;
                    flex-grow: 1;
                }
                .blog-card__meta {
                    font-size: 0.8rem;
                    color: #888;
                    margin-bottom: 0.75rem;
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                }
                .blog-card__title {
                    font-size: 1.25rem;
                    font-weight: 700;
                    line-height: 1.4;
                    margin-bottom: 0.75rem;
                    color: #333;
                    transition: color 0.2s ease;
                }
                .blog-card__title a {
                    color: inherit;
                    text-decoration: none;
                }
                .blog-card__title a:hover {
                    color: #ffd333; /* Primary brand yellow color or hover shade */
                }
                .blog-card__excerpt {
                    font-size: 0.9rem;
                    color: #666;
                    line-height: 1.6;
                    margin-bottom: 1.25rem;
                    flex-grow: 1;
                }
                .blog-card__link {
                    font-weight: 600;
                    font-size: 0.9rem;
                    color: #333;
                    text-decoration: none;
                    display: inline-flex;
                    align-items: center;
                    gap: 0.25rem;
                    transition: gap 0.2s ease, color 0.2s ease;
                }
                .blog-card__link:hover {
                    color: #ffd333;
                    gap: 0.5rem;
                }
                .blogs-pagination .pagination {
                    justify-content: center;
                    margin-top: 3rem;
                }
            </style>

            <div class="row">
                @foreach($blogs as $blog)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <article class="blog-card">
                            <div class="blog-card__image-wrapper">
                                <a href="{{ route('blogs.show', $blog) }}" wire:navigate>
                                    @if($blog->image)
                                        <img src="{{ $blog->image }}" alt="{{ $blog->title }}" class="blog-card__image">
                                    @else
                                        <img src="https://placehold.co/800x450?text=No+Image" alt="{{ $blog->title }}" class="blog-card__image">
                                    @endif
                                </a>
                            </div>
                            <div class="blog-card__body">
                                <div class="blog-card__meta">
                                    <i class="far fa-calendar-alt"></i>
                                    <span>{{ $blog->created_at->format('M d, Y') }}</span>
                                </div>
                                <h2 class="blog-card__title">
                                    <a href="{{ route('blogs.show', $blog) }}" wire:navigate>{{ $blog->title }}</a>
                                </h2>
                                <p class="blog-card__excerpt">
                                    {{ Str::limit(strip_tags($blog->content), 120) }}
                                </p>
                                <div>
                                    <a href="{{ route('blogs.show', $blog) }}" class="blog-card__link" wire:navigate>
                                        Read More <i class="fa fa-arrow-right" style="font-size: 0.8rem;"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>

            <div class="blogs-pagination">
                {{ $blogs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
