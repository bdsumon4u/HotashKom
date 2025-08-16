@extends('layouts.yellow.master')

@section('title', 'Products')

@push('styles')
<style>
    /* Modern product grid improvements */
    .products-section {
        padding: 2rem 0;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    }



    .category-section {
        margin-bottom: 4rem;
        padding: 2rem 0;
    }

    .category-grid-modern {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    .category-item-modern {
        text-align: center;
    }

    .category-image-container {
        position: relative;
        aspect-ratio: 1;
        width: 100%;
        margin-bottom: 1rem;
        overflow: hidden;
        background: #f8f9fa;
        border-radius: 0;
    }

    .category-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .category-item-modern:hover .category-image-container img {
        transform: scale(1.05);
    }

    .category-info {
        padding: 0;
    }

    .category-name {
        font-size: 1.1rem;
        font-weight: 500;
        color: #333;
        margin-bottom: 1rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .category-shop-btn {
        background: #fff;
        border: 1px solid #333;
        padding: 0.6rem 1.5rem;
        font-size: 0.9rem;
        font-weight: 400;
        color: #333;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .category-shop-btn:hover {
        background: #333;
        color: #fff;
        text-decoration: none;
    }



    .product-card-enhanced {
        background: #fff;
        border-radius: 0;
        overflow: hidden;
        box-shadow: none;
        transition: all 0.3s ease;
        margin-bottom: 2rem;
        position: relative;
        border: 1px solid #f0f0f0;
    }

    .product-card-enhanced:hover {
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .product-image-container {
        position: relative;
        overflow: hidden;
        background: #f8f9fa;
    }

    .product-image-container {
        aspect-ratio: 1;
        width: 100%;
    }

    .product-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .product-card-enhanced:hover .product-image-container img {
        transform: scale(1.05);
    }

    .product-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        background: linear-gradient(135deg, #ff6b6b, #ee5a52);
        color: white;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }



    .product-info-enhanced {
        padding: 1rem 1rem 1.5rem 1rem;
        text-align: left;
    }

    .product-title-enhanced {
        font-size: 1rem;
        font-weight: 400;
        color: #333;
        margin-bottom: 0.5rem;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 2.8rem;
    }

    .product-title-enhanced a {
        color: inherit;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .product-title-enhanced a:hover {
        color: #007bff;
    }

    .product-price-enhanced {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1rem;
        line-height: 1.4;
        color: #333;
    }

    .product-price-enhanced.has-special .product-price-new {
        color: #333;
        font-weight: 600;
        margin-right: 0.5rem;
    }

    .product-price-enhanced.has-special .product-price-old {
        color: #999;
        text-decoration: line-through;
        font-weight: 400;
        font-size: 0.9rem;
    }

    .product-price-enhanced:not(.has-special) {
        color: #333;
        font-weight: 600;
    }

    .product-badge-sale {
        position: absolute;
        top: 8px;
        right: 8px;
        background: #ff0000;
        color: white;
        padding: 0.2rem 0.5rem;
        border-radius: 0;
        font-size: 0.7rem;
        font-weight: 600;
        z-index: 2;
        text-transform: uppercase;
    }

    .product-actions-enhanced {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .cart-btn {
        width: 40px;
        height: 40px;
        border: 1px solid #ddd;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        color: #666;
    }

    .cart-btn:hover {
        background: #f5f5f5;
        border-color: #ccc;
    }

    .buy-now-btn {
        flex: 1;
        background: #fff;
        border: 1px solid #ddd;
        padding: 0.6rem 1rem;
        font-size: 0.9rem;
        font-weight: 400;
        color: #333;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .buy-now-btn:hover {
        background: #f5f5f5;
        border-color: #ccc;
        color: #333;
        text-decoration: none;
    }

    .products-grid-modern {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        padding: 1rem 0;
    }

    .section-title {
        text-align: center;
        margin-bottom: 2rem;
    }

    .section-title h2 {
        font-size: 2rem;
        font-weight: 400;
        color: #333;
        margin-bottom: 0;
        position: relative;
    }

            @media (max-width: 768px) {
        .category-grid-modern {
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .products-grid-modern {
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .section-title h2 {
            font-size: 2rem;
        }
    }

    @media (max-width: 576px) {
        .category-grid-modern {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .products-grid-modern {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .product-info-enhanced {
            padding: 1rem;
        }

        .category-section {
            padding: 1rem 0;
        }
    }

    /* Empty State Styles */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        background: #fff;
        border-radius: 8px;
        margin: 2rem auto;
        max-width: 600px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .empty-state-icon {
        font-size: 4rem;
        color: #e2e8f0;
        margin-bottom: 1rem;
    }

    .empty-state-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 0.5rem;
    }

    .empty-state-message {
        font-size: 1rem;
        color: #718096;
        margin-bottom: 1.5rem;
        line-height: 1.6;
    }

    .empty-state-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 0.8rem 2rem;
        border-radius: 25px;
        font-weight: 500;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.9rem;
    }

    .empty-state-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        color: white;
        text-decoration: none;
    }

    .empty-categories {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border: 2px dashed #e2e8f0;
    }

    .empty-products {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border: 2px dashed #e2e8f0;
    }

    .empty-state-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
    }

    @media (max-width: 576px) {
        .empty-state-actions {
            flex-direction: column;
            gap: 0.5rem;
        }

        .empty-state-actions .empty-state-btn {
            margin-right: 0 !important;
        }
    }
</style>
@endpush

@section('content')
        @if($categories->isNotEmpty())
    <section class="category-section">
        <div class="category-grid-modern">
            @foreach ($categories as $category)
                <div class="category-item-modern">
                    <div class="category-image-container">
                        <a href="{{route('products.index', ['filter_category' => $category->id])}}">
                            <img src="{{ $category->image_src }}"
                                 alt="{{ $category->name }}"
                                 loading="lazy">
                        </a>
                    </div>
                    <div class="category-info">
                        <h3 class="category-name">{{ $category->name }}</h3>
                        <a href="{{route('products.index', ['filter_category' => $category->id])}}"
                           class="category-shop-btn">Shop Now</a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif
    @if($products->isNotEmpty())
    <section class="products-section">
        <div class="container">
            <div class="section-title">
                <h2>
                    @if(request('search'))
                        Found {{ $products->total() }} result(s) for "{{ request('search', 'NULL') }}"
                    @elseif($category = request()->route()->parameter('category'))
                        Showing from "{{ $category->name }}" category.
                    @elseif($brand = request()->route()->parameter('brand'))
                        Showing from "{{ $brand->name }}" brand.
                    @elseif(request('filter_category'))
                        @php
                            $categoryNames = $categories->pluck('name')->implode(', ');
                        @endphp
                        {{ $categoryNames ?: 'Filtered Products' }}
                    @else
                        {{ $section?->title ?? 'Products' }} &mdash; Showing {{ $products->count() }} of {{ $products->total() }} products
                    @endif
                </h2>
            </div>

            <div class="products-grid-modern">
                @foreach($products as $product)
                    <div class="product-card-enhanced">
                        <div class="product-image-container">
                            <a href="{{route('products.show', $product)}}">
                                <img src="{{asset($product->base_image->src)}}"
                                     alt="{{$product->name}}"
                                     loading="lazy">
                            </a>

                            @if($product->price != $product->selling_price)
                                @php($percent = round((($product->price - $product->selling_price) * 100) / $product->price, 0, PHP_ROUND_HALF_UP))
                                <div class="product-badge-sale">
                                    {{$percent}}% OFF
                                </div>
                            @endif
                        </div>

                        <div class="product-info-enhanced">
                            <h3 class="product-title-enhanced">
                                <a href="{{route('products.show', $product)}}">{{$product->name}}</a>
                            </h3>

                            <div class="product-price-enhanced {{ $product->selling_price == $product->price ? '' : 'has-special' }}">
                                @if ($product->selling_price == $product->price)
                                    {!! theMoney($product->price) !!}
                                @else
                                    <span class="product-price-new">{!! theMoney($product->selling_price) !!}</span>
                                    <span class="product-price-old">{!! theMoney($product->price) !!}</span>
                                @endif
                            </div>

                            <div class="product-actions-enhanced">
                                <button class="cart-btn" title="Add to Cart">
                                    <i class="fa fa-shopping-cart"></i>
                                </button>
                                <a href="{{route('products.show', $product)}}" class="buy-now-btn">
                                    Buy Now <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @else
    <section class="products-section">
        <div class="container">
            <div class="empty-state empty-products">
                <div class="empty-state-icon">
                    <i class="fa fa-shopping-bag"></i>
                </div>
                <h3 class="empty-state-title">No Products Found</h3>
                <p class="empty-state-message">
                    @if(request('filter_category'))
                        Sorry, we couldn't find any products in this section. Try browsing other sections or check back later for new arrivals!
                    @else
                        We're currently updating our product catalog. Please check back soon for amazing deals and new products!
                    @endif
                </p>
                <div class="empty-state-actions">
                    @if(request('filter_category'))
                        <a href="{{route('products.index')}}" class="empty-state-btn" style="margin-right: 1rem;">
                            View All Products
                        </a>
                        <a href="javascript:history.back()" class="empty-state-btn" style="background: #6c757d;">
                            Go Back
                        </a>
                    @else
                        <a href="{{route('home')}}" class="empty-state-btn">
                            Return to Home
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>
    @endif
@endsection
