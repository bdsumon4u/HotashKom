@extends('layouts.yellow.master')

@section('title', 'Home')

@push('styles')
  <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
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

@section('content')

@include('partials.slides')

@if(isOninda() && config('app.resell') && auth('user')->guest())
@include('partials.auth-forms')
@endif

<!-- .block-features -->
@if(($services = setting('services'))->enabled ?? false)
<div class="block block-features block-features--layout--classic d-none d-md-block">
    <div class="container">
        <div class="block-features__list">
            @foreach(config('services.services', []) as $num => $icon)
                <div class="block-features__item">
                    <div class="block-features__icon">
                        <svg width="48px" height="48px">
                            <use xlink:href="{{ asset($icon) }}"></use>
                        </svg>
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
@if(($show_option = setting('show_option'))->category_carousel ?? false)
<div class="block block-products-carousel" data-layout="grid-cat">
    <div class="container">
        <div class="block-header">
            <h3 class="block-header__title" style="padding: 0.375rem 1rem;">
                <a href="{{ route('categories') }}">Categories</a>
            </h3>
            <div class="block-header__divider"></div>
            <div class="block-header__arrows-list">
                <button class="block-header__arrow block-header__arrow--left" type="button">
                    <svg width="7px" height="11px">
                        <use xlink:href="{{ asset('strokya/images/sprite.svg#arrow-rounded-left-7x11') }}"></use>
                    </svg>
                </button>
                <button class="block-header__arrow block-header__arrow--right" type="button">
                    <svg width="7px" height="11px">
                        <use xlink:href="{{ asset('strokya/images/sprite.svg#arrow-rounded-right-7x11') }}"></use>
                    </svg>
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
                                <a href="{{ route('categories.products', $category) }}">
                                    <img src="{{ cdn($category->image_src, 100, 100) }}" alt="Product Image">
                                </a>
                            </div>
                            <div class="product-card__info">
                                <div class="product-card__name">
                                    <h6 style="overflow: hidden;text-overflow:ellipsis;">
                                        <a href="{{ route('categories.products', $category) }}" title="{{$category->name}}">{{ $category->name }}</a>
                                    </h6>
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
<x-infinite-scroll-section :section="$section" />
@else
    @php($products = $section->products())
    <!-- .block-products-carousel -->
    @includeWhen($section->type == 'carousel-grid', 'partials.products.carousel-grid', [
        'title' => $section->title,
        'products' => $products,
        'rows' => optional($section->data)->rows,
        'cols' => optional($section->data)->cols,
    ])
@endif
@if ($section->type == 'banner')
    @php($pseudoColumns = (array)$section->data->columns)
    <div class="block block-banner">
        <div class="container-fluid">
            <div class="row">
                @foreach($pseudoColumns['width'] as $i => $width)
                <div class="col-md-{{$width}} mb-3">
                    @php($link = $pseudoColumns['link'][$i])
                    @php($link = $link && $link != '#' ? $link : null)
                    @php($link = $link ? url($link) : null)
                    @php($categories = implode(',', ((array)$pseudoColumns['categories'] ?? [])[$i] ?? []))
                    <a href="{{ $link ?? route('products.index', $categories ? ['filter_category' => $categories] : []) }}">
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
    @php($page = \App\Models\Page::find($section->data->page_id ?? null))
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
<!-- .block-products-carousel / end -->
@endforeach

@if(($show_option = setting('show_option'))->brand_carousel ?? false)
<div class="block block-products-carousel" data-layout="grid-cat">
    <div class="container">
        <div class="block-header">
            <h3 class="block-header__title" style="padding: 0.375rem 1rem;">
                <a href="{{ route('brands') }}">Brands</a>
            </h3>
            <div class="block-header__divider"></div>
            <div class="block-header__arrows-list">
                <button class="block-header__arrow block-header__arrow--left" type="button">
                    <svg width="7px" height="11px">
                        <use xlink:href="{{ asset('strokya/images/sprite.svg#arrow-rounded-left-7x11') }}"></use>
                    </svg>
                </button>
                <button class="block-header__arrow block-header__arrow--right" type="button">
                    <svg width="7px" height="11px">
                        <use xlink:href="{{ asset('strokya/images/sprite.svg#arrow-rounded-right-7x11') }}"></use>
                    </svg>
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
                                <a href="{{ route('brands.products', $brand) }}">
                                    <img src="{{ cdn($brand->image_src, 100, 100) }}" alt="Product Image">
                                </a>
                            </div>
                            <div class="product-card__info">
                                <div class="product-card__name">
                                    <h6 style="overflow: hidden;text-overflow:ellipsis;">
                                        <a href="{{ route('brands.products', $brand) }}" title="{{$brand->name}}">{{ $brand->name }}</a>
                                    </h6>
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

@endsection

@push('scripts')
  <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
  <script>
    AOS.init();
  </script>
@endpush
