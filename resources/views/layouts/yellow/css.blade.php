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
</style>
