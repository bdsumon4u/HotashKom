@php
    $fontawesomeCss = cdnAsset('fontawesome.css', 'assets/css/fontawesome.css');
@endphp

<!-- Google font-->
<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css?family=Rubik:400,400i,500,500i,700,700i&amp;display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900&amp;display=swap" rel="stylesheet">
<!-- Font Awesome-->
<link rel="stylesheet" type="text/css" href="{{ $fontawesomeCss }}" crossorigin="anonymous" referrerpolicy="no-referrer">
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