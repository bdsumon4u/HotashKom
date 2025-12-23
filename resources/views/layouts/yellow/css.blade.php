<link rel="stylesheet" href="{{ cdnAsset('bootstrap.css', 'strokya/vendor/bootstrap-4.2.1/css/bootstrap.min.css') }}" crossorigin="anonymous" referrerpolicy="no-referrer">
{{-- Defer Owl Carousel CSS to prevent render blocking - load asynchronously --}}
@php
    $owlCarouselCss = cdnAsset('owl-carousel.css', 'strokya/vendor/owl-carousel-2.3.4/assets/owl.carousel.min.css');
@endphp
<link rel="preload" href="{{ $owlCarouselCss }}" as="style" crossorigin="anonymous" onload="this.onload=null;this.rel='stylesheet'">
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
</style>
