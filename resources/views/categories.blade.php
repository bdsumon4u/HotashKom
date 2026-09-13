@extends('layouts.yellow.master')

@section('title', 'Categories')

@section('seo_tags')
    <title>Browse All Product Categories | {{ $company->name ?? config('app.name') }}</title>
    <meta name="description" content="Explore {{ $company->name ?? config('app.name') }} product categories including home, kitchen, gadgets, beauty, kids, fashion, lifestyle, daily-use essentials with nationwide delivery in Bangladesh.">
@endsection

@push('styles')
<style>
/* Premium categories page styling */
.bb-categories-page {
    padding: 14px 0 38px;
    background:
        radial-gradient(circle at top right, rgba(29,191,115,.09), transparent 28%),
        linear-gradient(180deg, #fbfffc 0%, #ffffff 70%);
}

.bb-categories-page .products-list__body {
    row-gap: 22px;
}

.bb-categories-page .product-card {
    display: flex;
    height: 100%;
    flex-direction: column;
    overflow: hidden;
    border: 1px solid #dbece2;
    border-radius: 15px;
    background: #ffffff;
    box-shadow: 0 6px 18px rgba(22, 58, 39, .08);
    transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
}

.bb-categories-page .product-card:hover {
    transform: translateY(-5px);
    border-color: #8fddb3;
    box-shadow: 0 15px 29px rgba(22, 58, 39, .15);
}

.bb-categories-page .product-card__image {
    aspect-ratio: 1 / 1;
    overflow: hidden;
    background: linear-gradient(145deg, #f6fdf8, #ecf8f0);
}

.bb-categories-page .product-card__image > a {
    display: block;
    width: 100%;
    height: 100%;
}

.bb-categories-page .product-card__image img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: contain;
    transition: transform .3s ease;
}

.bb-categories-page .product-card:hover .product-card__image img {
    transform: scale(1.055);
}

.bb-categories-page .product-card__info {
    display: flex;
    min-height: 68px;
    align-items: center;
    justify-content: center;
    padding: 12px 13px;
    border-top: 1px solid #e6f1ea;
    background: #ffffff;
}

.bb-categories-page .product-card__name {
    width: 100%;
    text-align: center;
    display: grid;
    place-content: center;
}

.bb-categories-page .product-card__name h6 {
    margin: 0;
    font-size: 16px;
    font-weight: 800;
    line-height: 1.35;
}

.bb-categories-page .product-card__name a {
    display: -webkit-box;
    overflow: hidden;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    color: #274535;
    text-decoration: none;
}

.bb-categories-page .product-card:hover .product-card__name a {
    color: #078b4b;
}

@media (max-width: 767.98px) {
    .bb-categories-page {
        padding: 8px 0 24px;
    }

    .bb-categories-page .products-list__body {
        row-gap: 12px;
    }

    .bb-categories-page .product-card {
        border-radius: 11px;
    }

    .bb-categories-page .product-card__info {
        min-height: 55px;
        padding: 8px 9px;
    }

    .bb-categories-page .product-card__name h6 {
        font-size: 13px;
    }
}
</style>
@endpush
@section('content')
@include('partials.page-header', [
    'paths' => [
        url('/') => 'Home',
    ],
    'active' => 'Categories',
    'page_title' => 'All Categories'
])

<div class="block block-products-carousel mt-1 bb-categories-page">
    <div class="container">
        <div class="products-view__list products-list" data-layout="grid-5-full" data-with-features="false">
            <div class="products-list__body">
                @foreach(categories() as $category)
                    <div class="products-list__item">
                        <div class="product-card">
                            <div class="product-card__image">
                                <a href="{{ route('category.show', $category) }}">
                                    <img src="{{ $category->image_src }}"
     alt="{{ $category->name }} category"
     loading="lazy"
     decoding="async">
                                </a>
                            </div>
                            <div class="product-card__info">
                                <div class="product-card__name">
                                    <h6>
                                        <a href="{{ route('category.show', $category) }}">{{ $category->name }}</a>
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
