@extends('layouts.yellow.master')

@section('title', 'Home')

@section('content')

@include('partials.slides')

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
                                    <img src="{{ $category->image_src }}" alt="Product Image">
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

@foreach($sections as $section)
<!-- .block-products-carousel -->
@includeWhen($section->type == 'carousel-grid', 'partials.products.carousel-grid', [
    'title' => $section->title,
    'products' => $section->products(),
    'rows' => optional($section->data)->rows,
    'cols' => optional($section->data)->cols,
])
@includeWhen($section->type == 'pure-grid', 'partials.products.pure-grid', [
    'title' => $section->title,
    'products' => $section->products(),
    'rows' => optional($section->data)->rows,
    'cols' => optional($section->data)->cols,
])
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
                                    <img src="{{ $brand->image_src }}" alt="Product Image">
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
