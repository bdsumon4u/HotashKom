@php
    $fontawesomeCss = cdnAsset('fontawesome.css', 'assets/css/fontawesome.css');
    $cdnProvider = config('cdn.provider', 'jsdelivr');
    $fontAwesomeVersion = config('cdn.assets.fontawesome.version', '6.5.1');

    // Determine base URL based on CDN provider
    $fontBaseUrl = match($cdnProvider) {
        'jsdelivr' => "https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@{$fontAwesomeVersion}/webfonts",
        'cdnjs' => "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/{$fontAwesomeVersion}/webfonts",
        'unpkg' => "https://unpkg.com/@fortawesome/fontawesome-free@{$fontAwesomeVersion}/webfonts",
        default => "https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@{$fontAwesomeVersion}/webfonts",
    };
@endphp

<!-- Google font-->
<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css?family=Rubik:400,400i,500,500i,700,700i&amp;display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900&amp;display=swap" rel="stylesheet">

{{-- Preload critical Font Awesome fonts for faster rendering --}}
@if(config('cdn.enabled', true))
    <link rel="preload" href="{{ $fontBaseUrl }}/fa-brands-400.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="{{ $fontBaseUrl }}/fa-solid-900.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="{{ $fontBaseUrl }}/fa-regular-400.woff2" as="font" type="font/woff2" crossorigin="anonymous">
@endif

<!-- Font Awesome-->
<link rel="stylesheet" type="text/css" href="{{ $fontawesomeCss }}" crossorigin="anonymous" referrerpolicy="no-referrer">

<!-- Optimize Font Awesome font loading with font-display: swap -->
<style>
    /* Override Font Awesome @font-face declarations to add font-display: swap */
    /* This ensures text is visible immediately while fonts load in the background */
    @font-face {
        font-family: 'Font Awesome 6 Brands';
        font-style: normal;
        font-weight: 400;
        font-display: swap;
        src: url('{{ $fontBaseUrl }}/fa-brands-400.woff2') format('woff2');
    }
    @font-face {
        font-family: 'Font Awesome 6 Free';
        font-style: normal;
        font-weight: 400;
        font-display: swap;
        src: url('{{ $fontBaseUrl }}/fa-regular-400.woff2') format('woff2');
    }
    @font-face {
        font-family: 'Font Awesome 6 Free';
        font-style: normal;
        font-weight: 900;
        font-display: swap;
        src: url('{{ $fontBaseUrl }}/fa-solid-900.woff2') format('woff2');
    }
    /* Font Awesome 5 compatibility */
    @font-face {
        font-family: 'Font Awesome 5 Brands';
        font-style: normal;
        font-weight: 400;
        font-display: swap;
        src: url('{{ $fontBaseUrl }}/fa-brands-400.woff2') format('woff2');
    }
    @font-face {
        font-family: 'Font Awesome 5 Free';
        font-style: normal;
        font-weight: 400;
        font-display: swap;
        src: url('{{ $fontBaseUrl }}/fa-regular-400.woff2') format('woff2');
    }
    @font-face {
        font-family: 'Font Awesome 5 Free';
        font-style: normal;
        font-weight: 900;
        font-display: swap;
        src: url('{{ $fontBaseUrl }}/fa-solid-900.woff2') format('woff2');
    }
</style>
<!-- ico-font-->
<link rel="stylesheet" type="text/css" href="{{ versionedAsset('assets/css/icofont.css') }}">
<!-- Themify icon-->
<link rel="stylesheet" type="text/css" href="{{ versionedAsset('assets/css/themify.css') }}">
<!-- Flag icon-->
<link rel="stylesheet" type="text/css" href="{{ versionedAsset('assets/css/flag-icon.css') }}">
<!-- Feather icon-->
<link rel="stylesheet" type="text/css" href="{{ versionedAsset('assets/css/feather-icon.css') }}">
<!-- Plugins css start-->
@stack('css')
<!-- Plugins css Ends-->
<!-- Bootstrap css-->
<link rel="stylesheet" type="text/css" href="{{ versionedAsset('assets/css/bootstrap.css') }}">
<!-- App css-->
<link rel="stylesheet" type="text/css" href="{{ versionedAsset('assets/css/style.css') }}">
<link id="color" rel="stylesheet" href="{{ versionedAsset('assets/css/color-1.css') }}" media="screen">
<!-- Responsive css-->
<link rel="stylesheet" type="text/css" href="{{ versionedAsset('assets/css/responsive.css') }}">
<link rel="stylesheet" type="text/css" href="{{ versionedAsset('assets/css/colorPick.min.css') }}">

<style>
    .notify-alert {
        max-width: 350px !important;
    }
</style>
