<link rel="stylesheet" href="{{ $bootstrapCss }}" crossorigin="anonymous" referrerpolicy="no-referrer">
{{-- Defer Owl Carousel CSS to prevent render blocking - load asynchronously --}}
@php
    $owlCarouselCss = cdnAsset('owl-carousel.css', 'strokya/vendor/owl-carousel-2.3.4/assets/owl.carousel.min.css');
@endphp
<link rel="preload" href="{{ $owlCarouselCss }}" as="style" data-async-css="true" crossorigin="anonymous" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="{{ $owlCarouselCss }}" crossorigin="anonymous"></noscript>
<link rel="stylesheet" href="{{ versionedAsset('strokya/css/style.css') }}">
{{-- <link rel="stylesheet" href="{{ asset('strokya/css/algolia.css') }}"> --}}

<style>
    .notify-alert {
        max-width: 350px !important;
    }

    .product-card {
        position: relative;
        background-color: #ffffff;
        border-radius: 4px;
        box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.5);
        transform: translate3d(0, 0, 0);
        transition:
            transform 0.2s ease,
            box-shadow 0.2s ease;
        will-change: transform, box-shadow;
    }

    .product-card:hover {
        transform: translate3d(-2px, -2px, 0);
        box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.5);
    }

    .product-card__ribbon {
        position: absolute;
        top: 1px;
        right: 3px;
        z-index: 2;
    }

    .badge--free-delivery {
        display: inline-block;
        background-color: #059669;
        color: #ffffff;
        font-size: 11px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 3px;
        line-height: 1.3;
        white-space: nowrap;
    }
</style>

<style>
/* NEHMART_CATEGORY_CAROUSEL_PREMIUM_V2 */
/* Visual styling only. Owl Carousel sizing and layout remain unchanged. */

.home-category-carousel .block-header {
    margin-bottom: 20px;
}

.home-category-carousel .block-header__title {
    padding: 0 !important;
}

.home-category-carousel .block-header__title a {
    display: inline-block;
    padding: 10px 18px;
    color: #ffffff;
    border-radius: 9px;
    box-shadow: 0 8px 18px rgba(0, 191, 115, .18);
    font-size: 20px;
    font-weight: 800;
    letter-spacing: -.3px;
    text-decoration: none;
}

.home-category-carousel .block-header__divider {
    height: 2px;
    background: linear-gradient(90deg, #00bf73, #98e5bf 65%, transparent);
}

.home-category-carousel .block-header__arrow {
    color: #ffffff;
    background: var(--brand);
    border: 1px solid var(--brand);
    border-radius: 8px;
    box-shadow: 0 6px 14px rgba(var(--brand-rgb), .16);
    transition: transform .2s ease, background .2s ease, box-shadow .2s ease;
}

.home-category-carousel .block-header__arrow svg {
    fill: currentColor;
}

.home-category-carousel .block-header__arrow:hover {
    background: var(--brand-dark);
    border-color: var(--brand-dark);
    box-shadow: 0 9px 18px rgba(var(--brand-rgb), .22);
    transform: translateY(-2px);
}

.home-category-carousel .product-card {
    overflow: hidden;
    background: #ffffff;
    border: 1px solid #dcece3;
    border-radius: 13px;
    box-shadow: 0 6px 16px rgba(23, 63, 42, .09);
    transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
}

.home-category-carousel .product-card__image {
    aspect-ratio: 1 / 1;
    overflow: hidden;
    background:
        radial-gradient(circle at 85% 12%, rgba(0, 191, 115, .13), transparent 34%),
        linear-gradient(145deg, #f8fffb, #edf9f2);
    flex: 0 0 auto;
}

.home-category-carousel .product-card__image > a {
    display: block;
    position: relative;
    width: 100%;
    height: 100%;
    padding-bottom: 0 !important;
}

.home-category-carousel .product-card__image img {
    position: absolute;
    inset: 0;
    display: block;
    width: 100% !important;
    height: 100% !important;
    object-fit: contain;
    transition: transform .25s ease;
}

.home-category-carousel .product-card__info {
    background: #ffffff;
}

.home-category-carousel .product-card__name h3 {
    color: #263c31;
    font-weight: 800 !important;
    line-height: 1.32;
}

.home-category-carousel .product-card__name h3 a {
    color: inherit;
    text-decoration: none;
}

@media (hover: hover) {
    .home-category-carousel .product-card:hover {
        border-color: #91dfb8;
        box-shadow: 0 13px 26px rgba(14, 112, 65, .14);
        transform: translateY(-4px);
    }

    .home-category-carousel .product-card:hover .product-card__image img {
        transform: scale(1.045);
    }
}

@media (max-width: 767.98px) {
    .home-category-carousel {
        margin-bottom: 35px !important;
    }

    .home-category-carousel .block-products-carousel__slider {
        min-height: 0 !important;
    }

    .home-category-carousel .owl-stage {
        padding-bottom: 8px !important;
        align-items: flex-start;
    }

    .home-category-carousel .owl-stage-outer {
        height: auto !important;
        margin-bottom: -8px !important;
        padding-top: 5px;
        overflow: hidden;
    }

    .home-category-carousel .owl-item,
    .home-category-carousel .products-list__item,
    .home-category-carousel .product-card {
        height: auto !important;
    }

    .home-category-carousel .block-header__title a {
        padding: 9px 14px;
        font-size: 18px;
    }
}
</style>
