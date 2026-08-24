@extends('layouts.yellow.master')

@push('head')
    @include('schema.blog', ['blog' => $blog])
@endpush

@section('seo_tags')
    {!! seo()->for($blog) !!}
@endsection

@section('title', $blog->title)

@push('styles')
    @once
        <link rel="stylesheet" href="{{ asset('css/hk-blog-article-premium.css') }}">
    @endonce
@endpush

@section('content')
{{-- COMPACT BLOG TOC START --}}
<style>
    /* Compact and user-friendly blog table of contents */
    .blog-toc {
        width: 100%;
        max-width: 680px;
        margin: 0 auto 1.5rem;
        padding: 12px 16px;
        border-radius: 8px;
        box-sizing: border-box;
    }

    .blog-toc__header,
    .blog-toc__heading {
        margin-bottom: 7px;
        padding-bottom: 7px;
    }

    .blog-toc__title,
    .blog-toc h2 {
        margin: 0;
        font-size: 1.05rem;
        line-height: 1.35;
    }

    .blog-toc__list,
    .blog-toc > ol,
    .blog-toc > ul {
        max-height: 280px;
        margin: 6px 0 0;
        padding: 0 8px 0 22px;
        overflow-y: auto;
        overscroll-behavior: contain;
        scrollbar-width: thin;
        scrollbar-color: #1DBF72 #edf8f2;
    }

    .blog-toc__list::-webkit-scrollbar,
    .blog-toc > ol::-webkit-scrollbar,
    .blog-toc > ul::-webkit-scrollbar {
        width: 6px;
    }

    .blog-toc__list::-webkit-scrollbar-track,
    .blog-toc > ol::-webkit-scrollbar-track,
    .blog-toc > ul::-webkit-scrollbar-track {
        background: #edf8f2;
        border-radius: 10px;
    }

    .blog-toc__list::-webkit-scrollbar-thumb,
    .blog-toc > ol::-webkit-scrollbar-thumb,
    .blog-toc > ul::-webkit-scrollbar-thumb {
        background: #1DBF72;
        border-radius: 10px;
    }

    .blog-toc__item,
    .blog-toc li {
        margin: 0 0 5px;
        line-height: 1.4;
    }

    .blog-toc__item--3 {
        margin-left: 12px;
    }

    .blog-toc__link,
    .blog-toc a {
        display: inline;
        font-size: 0.92rem;
        line-height: 1.4;
    }

    @media (max-width: 767px) {
        .blog-toc {
            margin-bottom: 1.25rem;
            padding: 10px 12px;
        }

        .blog-toc__title,
        .blog-toc h2 {
            font-size: 1rem;
        }

        .blog-toc__list,
        .blog-toc > ol,
        .blog-toc > ul {
            max-height: 220px;
            padding-left: 20px;
        }

        .blog-toc__link,
        .blog-toc a {
            font-size: 0.88rem;
        }

        .blog-toc__item--3 {
            margin-left: 8px;
        }
    }
</style>
{{-- COMPACT BLOG TOC END --}}

<div class="block nm-blog-article-page">
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
            .blog-toc {
                margin: 0 0 2.25rem;
                border: 1px solid rgba(29, 191, 114, 0.32);
                border-left: 4px solid #1DBF72;
                border-radius: 8px;
                background: #f6fffa;
                overflow: hidden;
            }
            .blog-toc__summary {
                padding: 1rem 1.25rem;
                color: #20352b;
                font-size: 1.1rem;
                font-weight: 700;
                cursor: pointer;
                list-style: none;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                user-select: none;
            }
            .blog-toc__summary::-webkit-details-marker {
                display: none;
            }
            .blog-toc__summary::after {
                content: '+';
                width: 28px;
                height: 28px;
                border-radius: 50%;
                background: #1DBF72;
                color: #fff;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                flex: 0 0 28px;
                font-size: 1.25rem;
                line-height: 1;
            }
            .blog-toc[open] .blog-toc__summary::after {
                content: '−';
            }
            .blog-toc__nav {
                padding: 1rem 1.25rem 1.15rem;
                border-top: 1px solid rgba(29, 191, 114, 0.2);
            }
            .blog-toc__list {
                margin: 0;
                padding: 0;
                list-style: none;
            }
            .blog-toc__item {
                margin: 0;
                padding: 0;
            }
            .blog-toc__item + .blog-toc__item {
                margin-top: 0.55rem;
            }
            .blog-toc__item--level-3 {
                margin-left: 1.35rem;
                font-size: 0.96em;
            }
            .blog-toc__link {
                color: #33483e;
                text-decoration: none;
                display: flex;
                align-items: flex-start;
                gap: 0.65rem;
                line-height: 1.5;
                transition: color 0.2s ease;
            }
            .blog-toc__link:hover,
            .blog-toc__link:focus {
                color: #15945a;
                text-decoration: underline;
            }
            .blog-toc__number {
                color: #15945a;
                font-weight: 700;
                flex: 0 0 auto;
            }
            .blog-post__content h2,
            .blog-post__content h3 {
                scroll-margin-top: 110px;
            }
            html {
                scroll-behavior: smooth;
            }
            @media (max-width: 767.98px) {
                .blog-toc {
                    margin-bottom: 1.75rem;
                }
                .blog-toc__summary {
                    padding: 0.9rem 1rem;
                    font-size: 1rem;
                }
                .blog-toc__nav {
                    padding: 0.9rem 1rem 1rem;
                }
                .blog-toc__item--level-3 {
                    margin-left: 0.85rem;
                }
            }
            @media (prefers-reduced-motion: reduce) {
                html {
                    scroll-behavior: auto;
                }
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

        <article class="blog-post nm-blog-article-premium">
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

            @if(count($tableOfContents ?? []) >= 2)
                <details class="blog-toc" open>
                    <summary class="blog-toc__summary">
                        সূচিপত্র (Table of Contents)
                    </summary>

                    <nav
                        class="blog-toc__nav"
                        aria-label="Table of Contents">
                        <ol class="blog-toc__list">
                            @foreach($tableOfContents as $item)
                                <li class="blog-toc__item blog-toc__item--level-{{ $item['level'] }}">
                                    <a
                                        class="blog-toc__link"
                                        href="#{{ $item['id'] }}">
                                        <span
                                            class="blog-toc__number"
                                            aria-hidden="true">
                                            {{ $loop->iteration }}.
                                        </span>
                                        <span>{{ $item['title'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ol>
                    </nav>
                </details>
            @endif

            <div class="blog-post__content mce-content-body">
                {!! $blogContent !!}
            </div>

            @include('blogs.partials.faqs', ['blog' => $blog])

            <section
                class="nm-blog-links-v3"
                aria-labelledby="nm-blog-more-guides">

                @if(
                    isset($relatedBlogs)
                    && $relatedBlogs->isNotEmpty()
                )
                    <div class="nm-blog-links-v3__related">

                        <h2
                            id="nm-blog-more-guides"
                            class="nm-blog-links-v3__heading">
                            আরও {{ $company->name ?? 'প্রাসঙ্গিক' }} গাইড
                        </h2>

                        <div class="nm-blog-links-v3__grid">

                            @foreach(
                                $relatedBlogs as $relatedBlog
                            )
                                <article
                                    class="nm-blog-links-v3__card">

                                    <a
                                        class="nm-blog-links-v3__card-link"
                                        href="{{ route('blogs.show', ['blog' => $relatedBlog->slug]) }}">

                                        {{ $relatedBlog->title }}

                                    </a>

                                </article>
                            @endforeach

                        </div>
                    </div>
                @endif


                @if(
                    ($previousBlog ?? null)
                    || ($nextBlog ?? null)
                )
                    <nav
                        class="nm-blog-links-v3__nav"
                        aria-label="Blog article navigation">

                        <div
                            class="nm-blog-links-v3__nav-side">

                            @if($previousBlog ?? null)

                                <a
                                    class="nm-blog-links-v3__nav-link"
                                    href="{{ route('blogs.show', ['blog' => $previousBlog->slug]) }}">

                                    <span
                                        class="nm-blog-links-v3__nav-label">
                                        ← আগের গাইড
                                    </span>

                                    <span
                                        class="nm-blog-links-v3__nav-title">
                                        {{ $previousBlog->title }}
                                    </span>

                                </a>

                            @endif

                        </div>


                        <div
                            class="nm-blog-links-v3__nav-side nm-blog-links-v3__nav-side--next">

                            @if($nextBlog ?? null)

                                <a
                                    class="nm-blog-links-v3__nav-link"
                                    href="{{ route('blogs.show', ['blog' => $nextBlog->slug]) }}">

                                    <span
                                        class="nm-blog-links-v3__nav-label">
                                        পরের গাইড →
                                    </span>

                                    <span
                                        class="nm-blog-links-v3__nav-title">
                                        {{ $nextBlog->title }}
                                    </span>

                                </a>

                            @endif

                        </div>

                    </nav>
                @endif

            </section>
        </article>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Blog TOC sticky override */
@media (min-width: 768px) {
    html body .blog-toc {
        position: static !important;
        inset: auto !important;
        top: auto !important;
        bottom: auto !important;
    }
}
</style>
@endpush

@push('styles')
<style>
/* Blog internal links styles */

.nm-blog-links-v3 {
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 1px solid #e7e7e7;
}

.nm-blog-links-v3__heading {
    margin: 0 0 1.25rem;
    font-size: 1.5rem;
    line-height: 1.4;
}

.nm-blog-links-v3__grid {
    display: grid;
    grid-template-columns:
        repeat(3, minmax(0, 1fr));
    gap: 1rem;
}

.nm-blog-links-v3__card {
    min-width: 0;
}

.nm-blog-links-v3__card-link {
    display: block;
    height: 100%;
    padding: 1rem 1.1rem;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #fff;
    color: #222;
    font-weight: 600;
    line-height: 1.5;
    text-decoration: none;
    transition:
        box-shadow .2s ease,
        transform .2s ease;
}

.nm-blog-links-v3__card-link:hover {
    color: #222;
    text-decoration: underline;
    transform: translateY(-1px);
    box-shadow:
        0 4px 15px rgba(0,0,0,.06);
}

.nm-blog-links-v3__nav {
    display: grid;
    grid-template-columns:
        repeat(2, minmax(0, 1fr));
    gap: 1rem;
    margin-top: 2rem;
}

.nm-blog-links-v3__nav-side {
    min-width: 0;
}

.nm-blog-links-v3__nav-side--next {
    text-align: right;
}

.nm-blog-links-v3__nav-link {
    display: flex;
    flex-direction: column;
    gap: .4rem;
    height: 100%;
    padding: 1rem 1.1rem;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #fff;
    color: #222;
    text-decoration: none;
}

.nm-blog-links-v3__nav-link:hover {
    color: #222;
    text-decoration: none;
    box-shadow:
        0 4px 15px rgba(0,0,0,.06);
}

.nm-blog-links-v3__nav-label {
    color: #6b7280;
    font-size: .82rem;
    font-weight: 600;
}

.nm-blog-links-v3__nav-title {
    font-weight: 600;
    line-height: 1.45;
}

@media (max-width: 767px) {

    .nm-blog-links-v3__grid,
    .nm-blog-links-v3__nav {
        grid-template-columns: 1fr;
    }

    .nm-blog-links-v3__nav-side--next {
        text-align: left;
    }
}
</style>
@endpush