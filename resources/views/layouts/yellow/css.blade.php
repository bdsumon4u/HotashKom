<link rel="stylesheet" href="{{ cdnAsset('bootstrap.css', 'strokya/vendor/bootstrap-4.2.1/css/bootstrap.min.css') }}" crossorigin="anonymous" referrerpolicy="no-referrer">
{{-- Defer Owl Carousel CSS to prevent render blocking - load asynchronously --}}
@php
    $owlCarouselCss = cdnAsset('owl-carousel.css', 'strokya/vendor/owl-carousel-2.3.4/assets/owl.carousel.min.css');
@endphp
<link rel="preload" href="{{ $owlCarouselCss }}" as="style" crossorigin="anonymous" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="{{ $owlCarouselCss }}" crossorigin="anonymous"></noscript>
<link rel="stylesheet" href="{{ versionedAsset('strokya/css/style.css') }}">
{{-- <link rel="stylesheet" href="{{ asset('strokya/css/algolia.css') }}"> --}}

<script>
    window.__isGbazTheme = {{ isGbazTheme() ? 'true' : 'false' }};
</script>
<style>
    .notify-alert {
        max-width: 350px !important;
    }

    @if (isGbazTheme())
        .product-card {
            position: relative;
            background-color: #ffffff;
            border: 1px solid #e5e7eb !important;
            border-radius: 6px !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03) !important;
            padding: 12px !important;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .product-card:hover {
            border-color: #f38020 !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
            transform: translateY(-2px);
        }

        .product-card__badges-list {
            position: absolute !important;
            top: 10px !important;
            left: 10px !important;
            right: 10px !important;
            z-index: 3 !important;
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-between !important;
            align-items: center !important;
            pointer-events: none;
            width: auto !important;
        }

        .gb-badge-offer {
            display: inline-block;
            background-color: #f38020 !important;
            color: #ffffff !important;
            font-size: 11px;
            font-weight: 500;
            padding: 3px 8px;
            border-radius: 4px;
            line-height: 1.2;
            white-space: nowrap;
        }

        .gb-badge-save {
            display: inline-block;
            background-color: #26a65b !important;
            color: #ffffff !important;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 4px;
            line-height: 1.2;
            white-space: nowrap;
            margin-left: auto;
        }

        .product-card__image {
            aspect-ratio: 1 / 1;
            overflow: hidden;
            padding: 8px !important;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
        }

        .product-card__image img {
            width: 100%;
            height: 100%;
            object-fit: contain !important;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-card__image img {
            transform: scale(1.04);
        }

        .product-card__info {
            padding: 0 !important;
            text-align: left !important;
            flex-grow: 1;
        }

        .product-card__name {
            font-size: 15px !important;
            font-weight: 500 !important;
            line-height: 1.35 !important;
            color: #1f2937 !important;
            margin-bottom: 6px !important;
            display: -webkit-box !important;
            -webkit-line-clamp: 2 !important;
            -webkit-box-orient: vertical !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: normal !important;
            min-height: 40px;
            text-align: left !important;
        }

        .product-card__name a {
            color: #1f2937 !important;
            text-decoration: none !important;
            transition: color 0.15s ease;
        }

        .product-card__name a:hover {
            color: #f38020 !important;
        }

        .product-card__actions {
            padding: 0 !important;
            margin-top: auto;
            width: 100%;
        }

        .product-card__prices {
            padding: 0 !important;
            margin-top: 2px !important;
            margin-bottom: 10px !important;
            font-size: 15px !important;
            font-weight: 700 !important;
            color: #f38020 !important;
            display: flex;
            align-items: baseline;
            gap: 6px;
            flex-wrap: wrap;
            text-align: left !important;
        }

        .product-card__new-price {
            color: #f38020 !important;
            font-weight: 700 !important;
            font-size: 15px !important;
        }

        .product-card__old-price {
            color: #9ca3af !important;
            font-weight: 400 !important;
            font-size: 13px !important;
            text-decoration: line-through !important;
        }

        .product-card__buttons {
            margin: 0 !important;
            width: 100%;
        }

        .product-card__buttons .btn,
        .product-card__addtocart,
        .product-card__ordernow {
            background-color: #ffffff !important;
            border: 1px solid #e59866 !important;
            color: #d97706 !important;
            border-radius: 4px !important;
            font-size: 13.5px !important;
            font-weight: 500 !important;
            padding: 7px 10px !important;
            height: auto !important;
            width: 100% !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            animation: none !important;
            transition: all 0.2s ease !important;
            box-shadow: none !important;
        }

        .product-card__buttons .btn:hover,
        .product-card__addtocart:hover,
        .product-card__ordernow:hover {
            background-color: #f38020 !important;
            border-color: #f38020 !important;
            color: #ffffff !important;
        }

        .product-card__ribbon {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 2;
        }

        .badge--free-delivery {
            display: inline-block;
            background-color: #059669;
            color: #ffffff;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 4px;
            line-height: 1.3;
            white-space: nowrap;
        }
    @else
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
    @endif
</style>
