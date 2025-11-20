<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="icon" href="{{asset($logo->favicon ?? '')}}" type="image/x-icon">
    <link rel="shortcut icon" href="{{asset($logo->favicon ?? '')}}" type="image/x-icon">
    <title>{{ $company->name ?? '' }} - Reseller Panel - @yield('title')</title>
    @php
        $jqueryJs = cdnAsset('jquery-3.5.1', 'assets/js/jquery-3.5.1.min.js');
    @endphp
    @include('layouts.partials.cdn-fallback', [
        'fallbackAssets' => [
            'jquery' => asset('assets/js/jquery-3.5.1.min.js'),
            'popper' => asset('assets/js/bootstrap/popper.min.js'),
        ],
    ])
    {{-- Global jQuery for SPA navigation --}}
    <script
        src="{{ $jqueryJs }}"
        data-navigate-once
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
        onerror="window.__loadLocalAsset && window.__loadLocalAsset('jquery')"
    ></script>
    <script data-navigate-once>
        (function () {
            if (window.runWhenJQueryReady) {
                window.__flushRunWhenJQueryQueue && window.__flushRunWhenJQueryQueue();
                return;
            }

            const queue = [];

            function flushQueue() {
                if (typeof window.jQuery === 'undefined') {
                    return;
                }

                while (queue.length) {
                    const callback = queue.shift();
                    try {
                        callback(window.jQuery);
                    } catch (error) {
                        console.error(error);
                    }
                }
            }

            function scheduleFlush() {
                queueMicrotask(flushQueue);
            }

            window.__flushRunWhenJQueryQueue = flushQueue;

            window.runWhenJQueryReady = function (callback) {
                if (typeof window.jQuery !== 'undefined') {
                    callback(window.jQuery);
                } else {
                    queue.push(callback);
                }
            };

            document.addEventListener('DOMContentLoaded', scheduleFlush, { once: true });
            document.addEventListener('livewire:navigate', scheduleFlush);
            scheduleFlush();
            if (document.readyState !== 'loading') {
                scheduleFlush();
            }
        })();
    </script>
    @include('layouts.light.css')
    <style>
      @media (min-width: 992px) {
        .toggle-sidebar {display: none;}
      }
      .but-not-fluid {
          max-height: 65px;
          height: 65px;
      }
      @media (max-width: 767px) {
          .but-not-fluid {
              max-height: 45px;
              height: 45px;
          }
      }
      .range_inputs {
        display: flex;
        justify-content: center;
      }

      .input-number {
        display: block;
        width: 100%;
        position: relative;
      }
      .product__quantity {
        width: 120px;
      }
      .cart-table__column {
        padding: 10px;
      }
      .cart-table__column.cart-table__column--price,
      .cart-table__column.cart-table__column--total {
        min-width: 120px;
      }
      .input-number__input {
        -moz-appearance: textfield;
        display: block;
        width: 100%;
        min-width: 88px;
        padding: 0 35px 0px 35px;
        text-align: center;
      }
      .input-number__add, .input-number__sub {
        position: absolute;
        height: 100%;
        width: 34px;
        top: 0;
        cursor: pointer;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        opacity: .3;
        transition: opacity .18s;
      }
      .input-number__add {
        right: 1px;
        border-left: 1px solid;
      }
      .input-number__sub {
        left: 1px;
        border-right: 1px solid;
      }
      .input-number__add:after, .input-number__add:before, .input-number__sub:after, .input-number__sub:before {
        display: block;
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translateX(-50%) translateY(-50%);
        background: currentColor;
      }
      .input-number__add:before, .input-number__sub:before {
        width: 8px;
        height: 2px;
      }
      .input-number__add:after {
        width: 2px;
        height: 8px;
      }

      /* Mobile sidebar toggle button visibility */
      @media (max-width: 991px) {
        .toggler-sidebar {
          display: block !important;
          visibility: visible !important;
          opacity: 1 !important;
        }
        .toggle-nav {
          display: block !important;
          visibility: visible !important;
          opacity: 1 !important;
        }
      }

      .checkout__totals-subtotals th,
      .checkout__totals-subtotals td {
        padding: 4px 0;
      }
    </style>
    @stack('styles')
    @bukStyles(true)
    @livewireStyles
  </head>
  <body class="light-only" main-theme-layout="ltr">
    @php $user = auth('user')->user() @endphp
    <!-- Loader starts-->
    <div class="loader-wrapper">
      <div class="loader-index"><span></span></div>
      <svg>
        <defs></defs>
        <filter id="goo">
          <fegaussianblur in="SourceGraphic" stddeviation="11" result="blur"></fegaussianblur>
          <fecolormatrix in="blur" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 19 -9" result="goo">    </fecolormatrix>
        </filter>
      </svg>
    </div>
    <!-- Loader ends-->
    <!-- page-wrapper Start-->
    <div class="page-wrapper compact-wrapper" id="pageWrapper">
      <!-- Page Header Start-->
      @include('layouts.reseller.header')
      <!-- Page Header Ends -->
      <!-- Page Body Start-->
      <div class="page-body-wrapper sidebar-icon">
        <!-- Page Sidebar Start-->
        @include('layouts.reseller.sidebar')
        <!-- Page Sidebar Ends-->
        <div class="page-body">
          <div class="container-fluid">
            <div class="page-header">
              <div class="row">
                <div class="col-lg-6">
                  @yield('breadcrumb-title')
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('reseller.dashboard') }}" wire:navigate.hover><i data-feather="home"></i></a></li>
                    @yield('breadcrumb-items')
                  </ol>
                </div>
                <div class="col-lg-6">
                  @yield('breadcrumb-right')
                </div>
              </div>
            </div>
          </div>
          <!-- Container-fluid starts-->
          <x-alert-box />
          <div class="alert-box"></div>
          @yield('content')
          <!-- Container-fluid Ends-->
        </div>
        <!-- footer start-->
        @include('layouts.light.footer')
      </div>
    </div>
    <!-- page-wrapper end-->
    @include('layouts.light.js')
    @stack('scripts')
    @livewireScripts
    <script>
      runWhenJQueryReady(function ($) {
          $(window)
              .off('notify.reseller')
              .on('notify.reseller', function (ev) {
                  for (let item of ev.detail) {
                      $.notify(item.message, {
                          type: item.type ?? 'info',
                      });
                  }
              });

          $(document)
              .off('click.resellerSidebar', '#sidebar-toggler')
              .on('click.resellerSidebar', '#sidebar-toggler', function (ev) {
                  ev.preventDefault();
                  const $nav = $(".main-nav");
                  const $header = $(".page-main-header");
                  $nav.toggleClass('close_icon');
                  $header.toggleClass('close_icon');
                  if ($nav.hasClass("close_icon")) {
                      $("body").css("overflow-y", "auto");
                  } else {
                      $("body").css("overflow-y", "hidden");
                  }
              });
      });
    </script>
  </body>
</html>
