<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="icon" href="{{ asset($logo->favicon ?? '') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset($logo->favicon ?? '') }}" type="image/x-icon">
    <title>{{ $company->name ?? 'HotashKom' }} - Investor Portal - @yield('title')</title>

    @include('layouts.light.css')
    @stack('css')

    <style>
        .page-wrapper .page-body-wrapper .page-body {
            padding: 15px 15px 0 15px;
        }
        .page-header {
            padding-top: 10px;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .card {
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #edf2f7;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }
        .card .card-header {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
        }
        .card .card-body {
            padding: 16px;
        }
        .card .card-footer {
            padding: 12px 16px;
        }
        .table th, .table td {
            padding: 0.65rem 0.85rem;
            vertical-align: middle !important;
        }
        .table thead th {
            white-space: nowrap !important;
            font-size: 13px;
            letter-spacing: 0.3px;
        }
        .table tbody td {
            white-space: nowrap !important;
            font-size: 13.5px;
        }
        .table-responsive {
            margin-bottom: 0;
        }
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 12px;
        }
        .badge {
            padding: 0.35em 0.65em;
            font-size: 82%;
            font-weight: 600;
        }

        /* Prevent Sidebar Horizontal Scroll */
        header.main-nav,
        .main-navbar,
        #mainnav,
        .nav-menu,
        .nav-menu.custom-scrollbar,
        .page-wrapper.compact-wrapper .page-body-wrapper.sidebar-icon header.main-nav .main-navbar .nav-menu {
            overflow-x: hidden !important;
        }
        .nav-menu > li,
        .nav-menu > li > a {
            max-width: 100% !important;
            box-sizing: border-box !important;
        }
    </style>
</head>

<body class="light-only" main-theme-layout="ltr">
    <div class="loader-wrapper">
        <div class="theme-loader">
            <div class="loader-p"></div>
        </div>
    </div>

    <div class="page-wrapper compact-wrapper" id="pageWrapper">
        @include('layouts.investor.header')
        <div class="page-body-wrapper sidebar-icon">
            @include('layouts.investor.sidebar')
            <div class="page-body">
                <div class="container-fluid">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-lg-6">
                                @yield('breadcrumb-title')
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('investor.dashboard') }}"><i data-feather="home"></i></a></li>
                                    @yield('breadcrumb-items')
                                </ol>
                            </div>
                            <div class="col-lg-6">
                                @yield('breadcrumb-right')
                            </div>
                        </div>
                    </div>
                </div>

                @if(session('success'))
                    <div class="container-fluid">
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong><i class="fa fa-check-circle"></i> Success:</strong> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="container-fluid">
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong><i class="fa fa-exclamation-triangle"></i> Error:</strong> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                @endif

                @yield('content')
            </div>
            @include('layouts.light.footer')
        </div>
    </div>

    @include('layouts.light.js')
    @stack('js')
    @stack('scripts')
</body>

</html>
