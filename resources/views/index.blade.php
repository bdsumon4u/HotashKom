@extends('layouts.yellow.master')

@push('head')
    @include('schema.home')
@endpush

@section('seo_tags')
    @if (!empty($company->seo_title))
        <title>{{ $company->seo_title }}</title>
        @if (!empty($company->meta_description))
            <meta name="description" content="{{ $company->meta_description }}">
        @endif
    @endif
@endsection

@section('title', 'Home')

@push('head')
    @if (empty($company->seo_title) && !empty($company->meta_description))
        <meta name="description" content="{{ $company->meta_description }}">
    @endif
@endpush

@push('head')
  {{-- Preconnect to unpkg.com for AOS.js to reduce latency --}}
  <link rel="preconnect" href="https://unpkg.com" crossorigin>
  <link rel="dns-prefetch" href="https://unpkg.com">
@endpush

@push('styles')
  {{-- Defer AOS CSS to prevent render blocking - load asynchronously --}}
  <link rel="preload" href="https://unpkg.com/aos@next/dist/aos.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript><link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css"></noscript>
  <style>
    .content-accordion .card {
      border: 1px solid #e3e3e3;
      margin-bottom: 1rem;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .content-accordion .card-header {
      background-color: #ffffff;
      border-bottom: 1px solid #e3e3e3;
      padding: 1rem 1.5rem;
      border-radius: 8px 8px 0 0;
    }
    .content-accordion .btn-link {
      color: #333;
      text-decoration: none;
      font-weight: 600;
      font-size: 1.1rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      width: 100%;
      text-align: left;
      padding: 0;
      background: none;
      border: none;
    }
    .content-accordion .btn-link:hover {
      color: #007bff;
      text-decoration: none;
    }
    .content-accordion .btn-link:focus {
      box-shadow: none;
      outline: none;
    }
    .content-accordion .btn-link::after {
      content: '−';
      font-size: 1.5rem;
      font-weight: bold;
      color: #666;
    }
    .content-accordion .btn-link.collapsed::after {
      content: '+';
    }
    .content-accordion .card-body {
      padding: 1.5rem;
      line-height: 1.6;
      color: #555;
      font-size: 0.95rem;
    }
    .content-accordion .collapse.show {
      display: block;
    }
    .content-accordion .card-body h1,
    .content-accordion .card-body h2,
    .content-accordion .card-body h3,
    .content-accordion .card-body h4,
    .content-accordion .card-body h5,
    .content-accordion .card-body h6 {
      color: #333;
      margin-bottom: 1rem;
      font-weight: 600;
    }
    .content-accordion .card-body p {
      margin-bottom: 1rem;
    }
    .content-accordion .card-body ul,
    .content-accordion .card-body ol {
      margin-bottom: 1rem;
      padding-left: 1.5rem;
    }
    .content-accordion .card-body li {
      margin-bottom: 0.5rem;
    }

  </style>
@endpush
@push('styles')
<style>
/* Premium home trust strip */
.nm-home-trust-strip {
    margin: 26px 0 38px;
}

.nm-home-trust-strip .block-features__list {
    display: flex;
    gap: 13px;
    padding: 14px;
    background:
        radial-gradient(circle at 6% 0%, rgba(var(--brand-rgb), .12), transparent 26%),
        linear-gradient(135deg, var(--brand-soft) 0%, #ffffff 58%, var(--brand-soft) 100%);
    border: 1px solid #d8eee2;
    border-radius: 18px;
    box-shadow: 0 10px 28px rgba(15, 72, 42, .08);
}

.nm-home-trust-strip .block-features__item {
    display: flex;
    flex: 1 1 0;
    align-items: center;
    min-width: 0;
    padding: 17px 18px;
    background: #ffffff;
    border: 1px solid #e2f0e8;
    border-radius: 14px;
    box-shadow: 0 4px 12px rgba(17, 76, 46, .045);
    transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
}

.nm-home-trust-strip .block-features__divider {
    display: none;
}

.nm-home-trust-strip .block-features__icon {
    display: inline-flex;
    flex: 0 0 58px;
    align-items: center;
    justify-content: center;
    width: 58px;
    height: 58px;
    margin-right: 15px;
    color: var(--brand-dark);
    background: linear-gradient(145deg, var(--brand-soft), #ffffff);
    border: 1px solid var(--brand-border);
    border-radius: 16px;
}

.nm-home-trust-strip .block-features__icon svg {
    width: 31px !important;
    height: 31px !important;
    fill: currentColor;
    stroke: currentColor;
}

.nm-home-trust-strip .block-features__content {
    min-width: 0;
}

.nm-home-trust-strip .block-features__title {
    margin-bottom: 5px;
    color: #234033;
    font-size: 17px;
    font-weight: 800;
    line-height: 1.25;
}

.nm-home-trust-strip .block-features__subtitle {
    color: #617369;
    font-size: 13px;
    font-weight: 500;
    line-height: 1.55;
}

@media (hover: hover) {
    .nm-home-trust-strip .block-features__item:hover {
        border-color: #a7e8c5;
        box-shadow: 0 10px 20px rgba(11, 118, 67, .11);
        transform: translateY(-3px);
    }
}
</style>
@push('styles')
<style>
/* Premium home products sections */
.block-products-carousel {
    padding: 24px 0 30px;
    border-radius: 16px;
    background: linear-gradient(180deg, #fbfffc 0%, #f4fbf7 100%);
}

.block-products-carousel .block-header {
    align-items: center;
    margin: 0 18px 22px;
}

.block-products-carousel .block-header__title {
    padding: 0 !important;
    margin: 0;
}

.block-products-carousel .block-header__title a {
    display: inline-flex;
    align-items: center;
    min-height: 50px;
    padding: 10px 21px;
    border-radius: 10px;
    background: linear-gradient(135deg, var(--brand-dark), var(--brand));
    color: #ffffff !important;
    font-size: 23px;
    font-weight: 800;
    letter-spacing: -.3px;
    box-shadow: 0 8px 20px rgba(var(--brand-rgb), .18);
}

.block-products-carousel .block-header__divider {
    height: 2px;
    margin: 0 20px;
    background: linear-gradient(90deg, var(--brand) 0%, var(--brand-border) 100%);
}

.block-products-carousel .btn-all {
    display: inline-flex;
    align-items: center;
    border: 1px solid var(--brand);
    border-radius: 8px;
    background: #ffffff;
    color: var(--brand-dark);
    font-weight: 700;
    padding: 8px 14px;
    box-shadow: 0 4px 12px rgba(var(--brand-rgb), .12);
    transition: transform .2s ease, background .2s ease, color .2s ease;
}

.block-products-carousel .btn-all:hover {
    background: var(--brand);
    color: #ffffff;
    transform: translateY(-1px);
}

.block-products-carousel .block-header__arrow {
    width: 42px;
    height: 42px;
    border: 0;
    border-radius: 9px;
    background: var(--brand);
    color: #ffffff;
    box-shadow: 0 6px 14px rgba(var(--brand-rgb), .18);
    transition: transform .2s ease, background .2s ease;
}

.block-products-carousel .block-header__arrow:hover {
    background: var(--brand-dark);
    transform: translateY(-1px);
}

.block-products-carousel .products-list__item {
    min-width: 0;
}

.block-products-carousel .product-card {
    overflow: hidden;
    border: 1px solid #d9e9df;
    border-radius: 13px;
    background: #ffffff;
    box-shadow: 0 4px 14px rgba(24, 55, 40, .09);
    transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
}

.block-products-carousel .product-card:hover {
    transform: translateY(-4px);
    border-color: var(--brand-border);
    box-shadow: 0 13px 25px rgba(24, 55, 40, .15);
}

.block-products-carousel .product-card__image {
    background: #f8fcf9;
}

.block-products-carousel .product-card__image img {
    transition: transform .28s ease;
}

.block-products-carousel .product-card:hover .product-card__image img {
    transform: scale(1.035);
}

.block-products-carousel .product-card__info {
    padding-top: 13px;
    padding-bottom: 6px;
}

.block-products-carousel .product-card__name a {
    display: -webkit-box;
    overflow: hidden;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    min-height: 42px;
    color: #263d30;
    font-weight: 700;
    line-height: 1.35;
}

.block-products-carousel .product-card__prices,
.block-products-carousel .product-card__new-price {
    color: var(--brand-dark);
    font-size: 16px;
    font-weight: 800;
}

.block-products-carousel .product-card__old-price {
    margin-left: 7px;
    color: #e94a4a;
    font-size: 13px;
    font-weight: 600;
}

.block-products-carousel .product-card__buttons {
    margin-top: 11px;
}

.block-products-carousel .product-card__addtocart,
.block-products-carousel .product-card__ordernow {
    width: 100%;
    min-height: 44px;
    border: 0;
    border-radius: 8px;
    box-shadow: 0 7px 14px rgba(var(--brand-rgb), .18);
    font-weight: 800;
}

@media (max-width: 767.98px) {
    .block-products-carousel {
        padding: 16px 0 20px;
        border-radius: 10px;
    }

    .block-products-carousel .block-header {
        margin: 0 10px 14px;
    }

    .block-products-carousel .block-header__title a {
        min-height: 40px;
        padding: 8px 13px;
        border-radius: 8px;
        font-size: 17px;
    }

    .block-products-carousel .block-header__divider {
        margin: 0 9px;
    }

    .block-products-carousel .btn-all {
        padding: 6px 9px;
        font-size: 12px;
    }

    .block-products-carousel .block-header__arrow {
        width: 35px;
        height: 35px;
        border-radius: 7px;
    }

    .block-products-carousel .product-card {
        border-radius: 10px;
    }

    .block-products-carousel .product-card__info {
        padding-top: 10px;
        padding-bottom: 4px;
    }

    .block-products-carousel .product-card__name a {
        min-height: 37px;
        font-size: 13px;
    }

    .block-products-carousel .product-card__prices,
    .block-products-carousel .product-card__new-price {
        font-size: 14px;
    }

    .block-products-carousel .product-card__addtocart,
    .block-products-carousel .product-card__ordernow {
        min-height: 39px;
        border-radius: 7px;
        font-size: 13px;
    }
}
</style>
@endpush
@section('content')

@include('partials.slides')

@if(isOninda() && config('app.resell') && auth('user')->guest())
@include('partials.auth-forms')
@endif

<!-- .block-features -->
@if(($services = setting('services'))->enabled ?? false)
@php
    $serviceIcons = config('services.service_icons', []);
@endphp
<div class="block block-features block-features--layout--classic nm-home-trust-strip d-none d-md-block">
    <div class="container">
        <div class="block-features__list">
            @foreach(config('services.services', []) as $num => $icon)
                <div class="block-features__item">
                    <div class="block-features__icon">
                        {!! str_replace('<svg ', '<svg width="48px" height="48px" ', $serviceIcons[$num] ?? '') !!}
                    </div>
                    <div class="block-features__content">
                        <div class="block-features__title">{{ $services->$num->title }}</div>
                        <div class="block-features__subtitle">{{ $services->$num->detail }}</div>
                    </div>
                </div>
                @if(!$loop->last)
                    <div class="block-features__divider"></div>
                @endif
            @endforeach
        </div>
    </div>
</div><!-- .block-features / end -->
@endif
@if(isOninda())
<div class="block">
    <div class="container">
        <x-reseller-verification-alert />
    </div>
</div>
@endif

@if(($show_option = setting('show_option'))->brand_carousel ?? false)
<div class="block block-products-carousel" data-layout="grid-cat">
    <div class="container">
        <div class="block-header">
            <h2 class="block-header__title" style="padding: 0.375rem 1rem;">
                <a href="{{ route('brands') }}">Brands</a>
            </h2>
            <div class="block-header__divider"></div>
            <div class="block-header__arrows-list">
                <button class="block-header__arrow block-header__arrow--left" type="button" aria-label="Previous">
                    <svg width="7px" height="11px" viewBox="0 0 7 11"><path d="M6.7.3c-.4-.4-.9-.4-1.3 0L0 5.5l5.4 5.2c.4.4.9.3 1.3 0 .4-.4.4-1 0-1.3l-4-3.9 4-3.9c.4-.4.4-1 0-1.3z"/></svg>
                </button>
                <button class="block-header__arrow block-header__arrow--right" type="button" aria-label="Next">
                    <svg width="7px" height="11px" viewBox="0 0 7 11"><path d="M.3 10.7c.4.4.9.4 1.3 0L7 5.5 1.6.3C1.2-.1.7 0 .3.3c-.4.4-.4 1 0 1.3l4 3.9-4 3.9c-.4.4-.4 1 0 1.3z"/></svg>
                </button>
            </div>
        </div>
        <div class="block-products-carousel__slider">
            <div class="block-products-carousel__preloader"></div>
            <div class="owl-carousel">
                @foreach(brands()->chunk(1) as $brands)
                <div>
                    @foreach($brands as $brand)
                    <div class="products-list__item">
                        <div class="product-card">
                            <div class="product-card__image">
                                <a href="{{ route('brand.show', $brand) }}">
                                    <img src="{{ cdn($brand->image_src, 100, 100) }}" alt="{{ optional($brand->image)->alt_text ?: $brand->name }}">
                                </a>
                            </div>
                            <div class="product-card__info">
                                <div class="product-card__name">
                                    <h3 style="overflow: hidden;text-overflow:ellipsis; font-size: 16px; font-weight: 700;">
                                        <a href="{{ route('brand.show', $brand) }}" title="{{$brand->name}}">{{ $brand->name }}</a>
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

@if(($show_option = setting('show_option'))->category_carousel ?? false)
<div class="block block-products-carousel home-category-carousel" data-layout="grid-cat">
    <div class="container">
        <div class="block-header">
            <h2 class="block-header__title" style="padding: 0.375rem 1rem;">
                <a href="{{ route('categories') }}">Categories</a>
            </h2>
            <div class="block-header__divider"></div>
            <div class="block-header__arrows-list">
                <button class="block-header__arrow block-header__arrow--left" type="button" aria-label="Previous">
                    <svg width="7px" height="11px" viewBox="0 0 7 11"><path d="M6.7.3c-.4-.4-.9-.4-1.3 0L0 5.5l5.4 5.2c.4.4.9.3 1.3 0 .4-.4.4-1 0-1.3l-4-3.9 4-3.9c.4-.4.4-1 0-1.3z"/></svg>
                </button>
                <button class="block-header__arrow block-header__arrow--right" type="button" aria-label="Next">
                    <svg width="7px" height="11px" viewBox="0 0 7 11"><path d="M.3 10.7c.4.4.9.4 1.3 0L7 5.5 1.6.3C1.2-.1.7 0 .3.3c-.4.4-.4 1 0 1.3l4 3.9-4 3.9c-.4.4-.4 1 0 1.3z"/></svg>
                </button>
            </div>
        </div>
        <div class="block-products-carousel__slider">
            <div class="block-products-carousel__preloader"></div>
            <div class="owl-carousel">
                @foreach(categories()->chunk(1) as $categories)
                <div>
                    @foreach($categories as $category)
                    <div class="products-list__item">
                        <div class="product-card">
                            <div class="product-card__image">
                                <a href="{{ route('category.show', $category) }}">
                                    <img
    src="{{ cdn($category->image_src, 320, 320) }}"
    srcset="{{ cdn($category->image_src, 320, 320) }} 320w, {{ cdn($category->image_src, 480, 480) }} 480w"
    sizes="(max-width: 575px) 33vw, (max-width: 991px) 25vw, 220px"
    alt="{{ optional($category->image)->alt_text ?: $category->name }}"
    width="320"
    height="320"
    loading="lazy"
    decoding="async"
    style="display: block; width: 100%; height: auto;"
>
                                </a>
                            </div>
                            <div class="product-card__info">
                                <div class="product-card__name">
                                    <h3 style="overflow: hidden;text-overflow:ellipsis; font-size: 16px; font-weight: 700;">
                                        <a href="{{ route('category.show', $category) }}" title="{{$category->name}}">{{ $category->name }}</a>
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

@foreach(sections() as $section)
@if($section->type == 'pure-grid')
    <!-- .block-products-carousel -->
    @if(config('app.infinite_scroll_section', false))
    <x-infinite-scroll-section :section="$section" />
    @else
    @include('partials.products.pure-grid', [
        'title' => $section->title,
        'products' => $section->products(),
        'cols' => optional($section->data)->cols ?? 5,
        'section' => $section,
    ])
    @endif
@else
    <!-- .block-products-carousel -->
    @includeWhen($section->type == 'carousel-grid', 'partials.products.carousel-grid', [
        'title' => $section->title,
        'products' => $section->products(),
        'rows' => optional($section->data)->rows,
        'cols' => optional($section->data)->cols,
    ])
@endif
@if ($section->type == 'banner')
    @php
        $pseudoColumns = (array) $section->data->columns;
    @endphp
    <div class="block block-banner">
        <div class="container-fluid">
            <div class="row">
                @foreach($pseudoColumns['width'] as $i => $width)
                <div class="col-md-{{$width}} mb-3">
                    @php
                        $link = $pseudoColumns['link'][$i] ?? null;
                        $link = $link && $link != '#' ? $link : null;
                        $link = $link ? url($link) : null;
                        $categories = implode(',', ((array) ($pseudoColumns['categories'] ?? []))[$i] ?? []);
                    @endphp
                    <a href="{{ $link ?? route('products.index', $categories ? ['filter_category' => $categories] : []) }}" @if(! $link) @endif>
                        <img
                            data-aos="{{$pseudoColumns['animation'][$i]}}"
                            class="border img-fluid w-100"
                            src="{{ cdn($pseudoColumns['image'][$i]) }}"
                            alt="Image"
                        >
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
@endif
@if ($section->type == 'content')
    @php
        $page = \App\Models\Page::find($section->data->page_id ?? null);
    @endphp
    @if($page)
    <div class="block">
        <div class="container">
            <div class="accordion content-accordion" id="content-accordion-{{ $section->id }}">
                <div class="card">
                    <div class="card-header" id="heading-{{ $section->id }}">
                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse-{{ $section->id }}" aria-expanded="true" aria-controls="collapse-{{ $section->id }}">
                            {{ $page->title }}
                        </button>
                    </div>
                    <div id="collapse-{{ $section->id }}" class="collapse show" aria-labelledby="heading-{{ $section->id }}" data-parent="#content-accordion-{{ $section->id }}">
                        <div class="card-body">
                            {!! $page->content !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endif
@endforeach


@include('partials.home-premium-bottom')

<!-- HotashKom Home SEO & Google Reviews Section -->
<style>
    .neh-home-trust-section {
        padding: 55px 0;
        background: #f7f9f8;
        border-top: 1px solid #e5e9e7;
    }

    .neh-home-intro {
        max-width: 900px;
        margin: 0 auto 35px;
        text-align: center;
    }

    .neh-home-main-heading {
        margin: 0 0 14px;
        color: #2f363d;
        font-size: 32px;
        line-height: 1.35;
        font-weight: 700;
    }

    .neh-home-intro p {
        margin: 0;
        color: #606971;
        font-size: 16px;
        line-height: 1.8;
    }

    .neh-google-review-card,
    .neh-google-map {
        height: 100%;
        overflow: hidden;
        background: #ffffff;
        border: 1px solid #e2e8e5;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(31, 45, 38, 0.07);
    }

    .neh-google-review-card {
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 35px;
    }

    .neh-google-review-label {
        margin-bottom: 12px;
        color: var(--brand);
        font-size: 14px;
        font-weight: 700;
        letter-spacing: .3px;
        text-transform: uppercase;
    }

    .neh-google-review-title {
        margin: 0 0 14px;
        color: #2f363d;
        font-size: 25px;
        line-height: 1.4;
        font-weight: 700;
    }

    .neh-google-review-text {
        margin: 0 0 24px;
        color: #606971;
        font-size: 15px;
        line-height: 1.8;
    }

    .neh-google-review-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        align-self: flex-start;
        min-height: 46px;
        padding: 11px 22px;
        color: #ffffff !important;
        background: var(--brand);
        border-radius: 6px;
        font-size: 15px;
        font-weight: 700;
        text-decoration: none !important;
        transition: background .2s ease, transform .2s ease;
    }

    .neh-google-review-button:hover {
        color: #ffffff !important;
        background: var(--brand-dark);
        transform: translateY(-1px);
    }

    .neh-google-review-button i {
        margin-right: 9px;
    }

    .neh-google-map iframe {
        display: block;
        width: 100%;
        height: 360px;
        border: 0;
    }

    @media (max-width: 991.98px) {
        .neh-google-review-card {
            margin-bottom: 20px;
        }
    }

    @media (max-width: 575.98px) {
        .neh-home-trust-section {
            padding: 38px 0;
        }

        .neh-home-intro {
            margin-bottom: 25px;
        }

        .neh-home-main-heading {
            font-size: 25px;
        }

        .neh-google-review-card {
            padding: 25px 20px;
        }

        .neh-google-review-title {
            font-size: 21px;
        }

        .neh-google-review-button {
            width: 100%;
        }

        .neh-google-map iframe {
            height: 300px;
        }
    }
</style>
@php
    $siteBrand = data_get(setting('company'), 'name') ?: config('app.name');
    $homeHeading = data_get(setting('company'), 'home_heading') ?: ($siteBrand . ' – বাংলাদেশের বিশ্বস্ত অনলাইন শপিং প্ল্যাটফর্ম');
    $gmapEcode = data_get(setting('company'), 'gmap_ecode') ?: null;
@endphp

<section class="neh-home-trust-section" aria-labelledby="neh-home-main-heading">
    <div class="container">
        <div class="neh-home-intro">
            <h1 id="neh-home-main-heading" class="neh-home-main-heading">
                {{ $homeHeading }}
            </h1>

            <p>
                হোম ও লাইফস্টাইল, গ্যাজেট, ইলেকট্রনিকস, ফ্যাশন এবং দৈনন্দিন প্রয়োজনীয় পণ্য
                সহজে অর্ডার করুন। সারা বাংলাদেশে Cash on Delivery সুবিধা রয়েছে।
            </p>
        </div>

        <div class="row align-items-stretch">
            <div class="{{ $gmapEcode ? 'col-lg-5 mb-4 mb-lg-0' : 'col-12' }}">
                <div class="neh-google-review-card">
                    <div class="neh-google-review-label">Google Business Profile</div>

                    <h2 class="neh-google-review-title">
                        Google-এ {{ $siteBrand }}-এর গ্রাহক মতামত
                    </h2>

                    <p class="neh-google-review-text">
                        {{ $siteBrand }} থেকে কেনাকাটা ও সেবা নেওয়া গ্রাহকদের অভিজ্ঞতা এবং মতামত
                        আমাদের Google Business Profile-এ দেখুন।
                    </p>

                    @if(isset($company->gmap_ecode) && !str_starts_with($company->gmap_ecode, '<iframe'))
                    <a
                        href="{{ $company->gmap_ecode }}"
                        class="neh-google-review-button"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        <i class="fas fa-star" aria-hidden="true"></i>
                        Google Reviews দেখুন
                    </a>
                    @endif
                </div>
            </div>

            @if($gmapEcode)
            <div class="col-lg-7">
                <div class="neh-google-map">
                    <button
                        type="button"
                        class="neh-google-map-loader"
                        data-map-src="{{ $gmapEcode }}"
                        aria-label="Load {{ $siteBrand }} interactive Google Map"
                        style="width:100%;height:100%;min-height:320px;border:0;border-radius:inherit;background:#f4fbf7;color:#263746;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;cursor:pointer;padding:25px;"
                    >
                        <span aria-hidden="true" style="font-size:42px;line-height:1;">📍</span>

                        <strong style="font-size:18px;">
                            {{ $siteBrand }} Google Map
                        </strong>

                        <span style="font-size:14px;">
                            Interactive map দেখতে ক্লিক করুন
                        </span>
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>

@endsection

@push('scripts')

<script>
    if (!window.hkMapLoaderBound) {
        window.hkMapLoaderBound = true;

        document.addEventListener('click', function (event) {
            const button = event.target.closest('.neh-google-map-loader');

            if (!button) {
                return;
            }

            const iframe = document.createElement('iframe');

            iframe.src = button.dataset.mapSrc;
            iframe.title = '{{ $siteBrand }} Google Business Profile location';
            iframe.loading = 'eager';
            iframe.referrerPolicy = 'strict-origin-when-cross-origin';
            iframe.setAttribute('allowfullscreen', '');
            iframe.style.cssText =
                'width:100%;height:100%;min-height:320px;border:0;display:block;';

            button.replaceWith(iframe);
        });
    }
</script>

  {{-- Defer AOS.js - it's only for animations, not critical for initial render --}}
  <script src="https://unpkg.com/aos@next/dist/aos.js" defer></script>
  <script defer>
    // Wait for AOS to load before initializing
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', function() {
        if (typeof AOS !== 'undefined') {
          AOS.init();
        } else {
          // Fallback: wait for script to load
          window.addEventListener('load', function() {
            if (typeof AOS !== 'undefined') {
              AOS.init();
            }
          });
        }
      });
    } else {
      // DOM already loaded
      if (typeof AOS !== 'undefined') {
        AOS.init();
      } else {
        window.addEventListener('load', function() {
          if (typeof AOS !== 'undefined') {
            AOS.init();
          }
        });
      }
    }
  </script>
@endpush
