@extends('layouts.yellow.master')

@section('title', 'Categories')

@section('content')
@include('partials.page-header', [
    'paths' => [
        url('/') => 'Home',
    ],
    'active' => 'Categories',
    'page_title' => 'All Categories'
])

<div class="block block-products-carousel mt-1">
    <div class="container">
        <div class="products-view__list products-list" data-layout="grid-5-full" data-with-features="false">
            <div class="products-list__body">
                @foreach(categories() as $category)
                    <div class="products-list__item">
                        <div class="product-card">
                            <div class="product-card__image">
                                <a href="{{ route('categories.products', $category) }}">
                                    <img src="{{ $category->image_src }}" alt="Product Image">
                                </a>
                            </div>
                            <div class="product-card__info">
                                <div class="product-card__name">
                                    <h6>
                                        <a href="{{ route('categories.products', $category) }}">{{ $category->name }}</a>
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
