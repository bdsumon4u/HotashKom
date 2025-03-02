<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="format-detection" content="telephone=no">
    <title>{{ $company->name }} - @yield('title')</title>
    <link rel="icon" type="image/png" href="{{ asset($logo->favicon) }}"><!-- fonts -->
    <!-- css -->
    @include('googletagmanager::head')
    <x-metapixel-head/>
    @include('layouts.yellow.css')
    <!-- js -->
    <!-- font - fontawesome -->
    <link rel="stylesheet" href="{{ asset('strokya/vendor/fontawesome-5.6.1/css/all.min.css') }}"><!-- font - stroyka -->
    <link rel="stylesheet" href="{{ asset('strokya/fonts/stroyka/stroyka.css') }}">
    @include('layouts.yellow.color')
    <style>
        .topbar__item {
            flex: none;
        }
        .page-header__container {
            padding-bottom: 12px;
        }
        .products-list__item {
            justify-content: space-between;
        }
        @media (max-width: 479px) {
            /* .products-list[data-layout=grid-5-full] .products-list__item {
                width: 46%;
                margin: 8px 6px;
            } */
            .product-card__buttons .btn {
                font-size: 0.75rem;
            }
        }
        @media (max-width: 575px) {
            .mobile-header__search {
                top: 55px;
            }
            .mobile-header__search-form .aa-input-icon {
                display: none;
            }
            .mobile-header__search-form .aa-hint, .mobile-header__search-form .aa-input {
                padding-right: 15px !important;
            }
            .block-products-carousel[data-layout=grid-4] .product-card .product-card__buttons .btn {
                height: auto;
            }
        }
        .product-card:before,
        .owl-carousel {
            z-index: 0;
        }
        .block-products-carousel[data-layout^=grid-] .product-card .product-card__info,
        .products-list[data-layout^=grid-] .product-card .product-card__info {
            padding: 0 14px;
        }
        .block-products-carousel[data-layout^=grid-] .product-card .product-card__actions,
        .products-list[data-layout^=grid-] .product-card .product-card__actions {
            padding: 0 14px 14px 14px;
        }
        .product-card__badges-list {
            flex-direction: row;
        }
        .product-card__name {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .product-card__buttons {
            margin-right: -12px !important;
            /* margin-bottom: -12px !important; */
            margin-left: -12px !important;
        }
        .product-card__buttons .btn {
            height: auto !important;
            font-size: 20px !important;
            padding: 0.25rem 0.15rem !important;
            border-radius: 0 !important;
            display: block;
            width: 100%;
        }
        .aa-input-container {
            width: 100%;
        }
        .algolia-autocomplete {
            width: 100%;
            display: flex !important;
        }
        #aa-search-input {
            box-shadow: none;
        }
        .indicator__area {
            padding: 0 8px;
        }
        .mobile-header__search.mobile-header__search--opened {
            height: 100%;
            display: flex;
            align-items: center;
        }
        .mobile-header__search-form {
            width: 100%;
        }
        .mobile-header__search-form .aa-input-container {
            display: flex;
        }
        .mobile-header__search-form .aa-input-search {
            box-shadow: none;
        }
        .mobile-header__search-form .aa-hint,
        .mobile-header__search-form .aa-input {
            height: 54px;
            padding-right: 32px;
        }
        .mobile-header__search-form .aa-input-icon {
            right: 62px;
        }
        .mobile-header__search-form .aa-dropdown-menu {
            background-color: #f7f8f9;
            z-index: 9999 !important;
        }
        .aa-input-container input {
            font-size: 15px;

        }
        .toast {
            position: absolute;
            top: 10%;
            right: 10%;
            z-index: 9999;
        }
        .header-fixed .site__body {
            padding-top: 11rem;
        }
        @media (max-width: 991px) {
            .header-fixed .site__header {
                position: fixed;
                width: 100%;
                z-index: 9999;
            }
            .header-fixed .site__body {
                padding-top: 85px;
            }
            .header-fixed .mobilemenu__body {
                top: 85px;
            }
        }

        .dropcart__products-list {
            max-height: 300px;
            overflow-y: auto;
        }

        /** StickyNav **/
        .site-header.sticky {
            position: fixed;
            top: 0;
            min-width: 100%;
        }
        .site-header.sticky .site-header__middle {
            height: 65px;
        }
        /*.site-header.sticky .site-header__nav-panel,*/
        .site-header.sticky .site-header__topbar {
            display: none;
        }
        ::placeholder {
            color: #777 !important;
        }


        .widget-connect{position:fixed;bottom:30px;z-index:99 !important;cursor:pointer}.widget-connect-right{right:27px;bottom:22px}.widget-connect-left{left:20px}@media (max-width:768px){.widget-connect-left{left:10px;bottom:10px}}.widget-connect.active .widget-connect__button{display:grid;place-content:center;padding-top:5px}.widget-connect__button{display:none;height:55px;width:55px;margin:auto;margin-bottom:15px;border-radius:50%;overflow:hidden;box-shadow:2px 2px 6px rgba(0, 0, 0, .4);font-size:28px;text-align:center;line-height:50px;color:#fff;outline:0
!important;background-position:center center;background-repeat:no-repeat;transition:all;transition-duration: .2s}@media (max-width:768px){.widget-connect__button{height:50px;width:50px}}.widget-connect__button-activator:hover,.widget-connect__button:hover{box-shadow:2px 2px 8px 2px rgba(0,0,0,.4)}.widget-connect__button:active{height:48px;width:48px;box-shadow:2px 2px 6px rgba(0, 0, 0, 0);transition:all;transition-duration: .2s}@media (max-width:768px){.widget-connect__button:active{height:45px;width:45px}}.widget-connect__button-activator{margin:auto;border-radius:50%;box-shadow:2px 2px 6px rgba(0, 0, 0, .4);background-position:center center;background-repeat:no-repeat;transition:all;transition-duration: .2s;text-align:right;z-index:99!important}.widget-connect__button-activator-icon{height:55px;width:55px;background-image:url(/multi-chat.svg);background-size:55%;background-position:center center;background-repeat:no-repeat;-webkit-transition-duration: .2s;-moz-transition-duration: .2s;-o-transition-duration: .2s;transition-duration: .2s}@media (max-width:768px){.widget-connect__button-activator-icon{height:50px;width:50px}}.widget-connect__button-activator-icon.active{background-image:url(/multi-chat.svg);background-size:45%;transform:rotate(90deg)}.widget-connect__button-telephone{background-color:#FFB200;background-image:url(/catalog/view/theme/default/image/widget-multi-chat/call.svg);background-size:55%}.widget-connect__button-messenger{background-color:#0866FF;background-image:url(/catalog/view/theme/default/image/widget-multi-chat/messenger.svg);background-size:65%;background-position-x:9px}.widget-connect__button-whatsapp{background-color:#25d366;background-image:url(/catalog/view/theme/default/image/widget-multi-chat/whatsapp.svg);background-size:65%}@-webkit-keyframes button-slide{0%{opacity:0;display:none;margin-top:0;margin-bottom:0;-ms-transform:translateY(15px);-webkit-transform:translateY(15px);-moz-transform:translateY(15px);-o-transform:translateY(15px);transform:translateY(15px)}to{opacity:1;display:block;margin-top:0;margin-bottom:10px;-ms-transform:translateY(0);-webkit-transform:translateY(0);-moz-transform:translateY(0);-o-transform:translateY(0);transform:translateY(0)}}@-moz-keyframes button-slide{0%{opacity:0;display:none;margin-top:0;margin-bottom:0;-ms-transform:translateY(15px);-webkit-transform:translateY(15px);-moz-transform:translateY(15px);-o-transform:translateY(15px);transform:translateY(15px)}to{opacity:1;display:block;margin-top:0;margin-bottom:9px;-ms-transform:translateY(0);-webkit-transform:translateY(0);-moz-transform:translateY(0);-o-transform:translateY(0);transform:translateY(0)}}@-o-keyframes button-slide{0%{opacity:0;display:none;margin-top:0;margin-bottom:0;-ms-transform:translateY(15px);-webkit-transform:translateY(15px);-moz-transform:translateY(15px);-o-transform:translateY(15px);transform:translateY(15px)}to{opacity:1;display:block;margin-top:0;margin-bottom:10px;-ms-transform:translateY(0);-webkit-transform:translateY(0);-moz-transform:translateY(0);-o-transform:translateY(0);transform:translateY(0)}}@keyframes button-slide{0%{opacity:0;display:none;margin-top:0;margin-bottom:0;-ms-transform:translateY(15px);-webkit-transform:translateY(15px);-moz-transform:translateY(15px);-o-transform:translateY(15px);transform:translateY(15px)}to{opacity:1;display:block;margin-top:0;margin-bottom:10px;-ms-transform:translateY(0);-webkit-transform:translateY(0);-moz-transform:translateY(0);-o-transform:translateY(0);transform:translateY(0)}}.button-slide{-webkit-animation-name:button-slide;-moz-animation-name:button-slide;-o-animation-name:button-slide;animation-name:button-slide;-webkit-animation-duration: .2s;-moz-animation-duration: .2s;-o-animation-duration: .2s;animation-duration: .2s;-webkit-animation-fill-mode:forwards;-moz-animation-fill-mode:forwards;-o-animation-fill-mode:forwards;animation-fill-mode:forwards}.button-slide-out{-webkit-animation-name:button-slide;-moz-animation-name:button-slide;-o-animation-name:button-slide;animation-name:button-slide;-webkit-animation-duration: .2s;-moz-animation-duration: .2s;-o-animation-duration: .2s;animation-duration: .2s;-webkit-animation-fill-mode:forwards;-moz-animation-fill-mode:forwards;-o-animation-fill-mode:forwards;animation-fill-mode:forwards;-webkit-animation-direction:reverse;-moz-animation-direction:reverse;-o-animation-direction:reverse;animation-direction:reverse}.widget-connect
.tooltip{position:absolute;z-index:99 !important;display:block;font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-size:12px;font-style:normal;font-weight:400;line-height:1.42857143;text-align:left;text-align:start;text-decoration:none;text-shadow:none;text-transform:none;letter-spacing:normal;word-break:normal;word-spacing:normal;word-wrap:normal;white-space:normal;filter:alpha(opacity=0);opacity:0;line-break:auto;padding:5px}.tooltip-inner{max-width:200px;padding:5px
10px;color:#fff;text-align:center;background-color:#333;border-radius:4px}.tooltip.left .tooltip-arrow{top:50%;right:0;margin-top:-5px;border-width:5px 0 5px 5px;border-left-color:#333}.tooltip.right .tooltip-arrow{top:50%;left:0;margin-top:-5px;border-width:5px 5px 5px 0;border-right-color:#333}@media only screen and (max-width: 575px){.widget-connect-right{bottom:50px !important}}
    </style>
    @stack('styles')
    @livewireStyles
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@100..900&display=swap" rel="stylesheet">
{!! $scripts ?? null !!}
</head>

<body class="header-fixed" style="margin: 0; padding: 0;">
    @include('googletagmanager::body')
    <x-metapixel-body/>
    <!-- quickview-modal -->
    <div id="quickview-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content"></div>
        </div>
    </div><!-- quickview-modal / end -->
    <!-- mobilemenu -->
    <div class="mobilemenu">
        <div class="mobilemenu__backdrop"></div>
        <div class="mobilemenu__body">
            <div class="mobilemenu__header">
                <div class="mobilemenu__title">Menu</div>
                <button type="button" class="mobilemenu__close">
                    <svg width="20px" height="20px">
                        <use xlink:href="{{ asset('strokya/images/sprite.svg#cross-20') }}"></use>
                    </svg>
                </button>
            </div>
            <div class="mobilemenu__content">
                <ul class="mobile-links mobile-links--level--0" data-collapse data-collapse-opened-class="mobile-links__item--open">
                    @include('partials.mobile-menu-categories')
                    @include('partials.header.menu.mobile')
                </ul>
            </div>
        </div>
    </div><!-- mobilemenu / end -->
    <!-- site -->
    <div class="site">
        <!-- mobile site__header -->
        @include('partials.header.mobile')
        <!-- mobile site__header / end -->
        <!-- desktop site__header -->
        @include('partials.header.desktop')
        <!-- desktop site__header / end -->
        <!-- site__body -->
        <div class="site__body">
            <div class="container">
                <x-alert-box class="mt-2 row" />
            </div>
            @yield('content')
        </div>
        <!-- site__body / end -->
        <!-- site__footer -->
        @include('partials.footer')
        <!-- site__footer / end -->
    </div><!-- site / end -->
    @livewireScripts
    @include('layouts.yellow.js')
    <script>
        $(window).on('notify', function (ev) {
            for (let item of ev.detail) {
                $.notify(item.message, {
                    type: item.type ?? 'info',
                });
            }
        });
        $(window).on('dataLayer', function (ev) {
            for (let item of ev.detail) {
                window.dataLayer.push(item);
            }
        });
        $(document).ready(function () {
            // $(document).on('change', '.option-picker', function (ev) {
            //     var options = [];
            //     $(document).find('.option-picker:checked').each((_, item) => options.push(item.value));
                
            //     $.get({
            //         url: '',
            //         data: {options},
            //         success: function(data) {
            //             $('.product__content').data('id', data.dataId);
            //             $('.product__content').data('max', data.dataMax);
            //             $('.product__info').remove();
            //             $('.xzoom-container').after(data.content);
            //         },
            //         dataType: 'json',
            //     });
            // });

            function onScroll() {
                // $('input, textarea').blur();
                var scrollTop = $(this).scrollTop()
                if (scrollTop > 32) {
                    $('.site__header.position-fixed .topbar').hide();
                } else {
                    $('.site__header.position-fixed .topbar').show();
                }
                if (scrollTop > 200) {
                    // $('.site-header').addClass('sticky');
                    // $('.site-header__phone').removeClass('d-none');
                    $('.departments').removeClass('departments--opened departments--fixed');
                    $('.departments__body').attr('style', '');
                } else {
                    // $('.site-header').removeClass('sticky');
                    // $('.site-header__phone').addClass('d-none');
                    if ($('.departments').data('departments-fixed-by') != '')
                        $('.departments').addClass('departments--opened departments--fixed');
                    $('.departments--opened.departments--fixed .departments__body').css('min-height', '458px');
                }
            }

            $(window).on('scroll', onScroll);
            onScroll();
        });
    </script>
    @stack('scripts')
    @php
        function phone88($phone) {
            $phone = preg_replace('/[^\d]/', '', $phone);
            if (strlen($phone) == 11) {
                $phone = '88' . $phone;
            }
            return $phone;
        }
        $messenger = $company->messenger ?? '';
        $phone = phone88($company->whatsapp ?? '');
    @endphp
    @if ($phone && strlen($messenger) > 13)
    <div class="widget-connect widget-connect-right">
        @if($messenger)
        <a class="widget-connect__button widget-connect__button-telemessenger button-slide-out" style="background: white; color: blue;" href="{{$messenger}}" data-toggle="tooltip" data-placement="left" title="" target="_blank" data-original-title="Messenger">
            <i class="fab fa-facebook-messenger"></i>
        </a>
        @endif
        @if($phone)
        <a class="widget-connect__button widget-connect__button-whatsapp button-slide-out" style="background: white; color: green;" href="https://wa.me/{{$phone}}" data-toggle="tooltip" data-placement="left" title="" target="_blank" data-original-title="WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>
        @endif
        <div class="widget-connect__button-activator" style="background-color: #ff0000;">
            <div class="widget-connect__button-activator-icon"></div>
        </div>
    </div>
    @elseif ($phone)
    <a
        href="https://api.whatsapp.com/send?phone={{$phone}}" target="_blank"
        style="position:fixed;width:60px;height:60px;bottom:40px;right:40px;background-color:#25d366;color:#FFF;border-radius:50px;text-align:center;font-size:30px;box-shadow: 2px 2px 3px #999;z-index:100;"
    >
        <i class="fab fa-whatsapp" style="margin-top: 1rem;"></i>
    </a>
    @elseif (strlen($messenger) > 13)
    <a
        href="{{$messenger}}" target="_blank"
        style="position:fixed;width:60px;height:60px;bottom:40px;right:40px;background-color:#0084ff;color:#FFF;border-radius:50px;text-align:center;font-size:30px;box-shadow: 2px 2px 3px #999;z-index:100;"
    >
        <i class="fab fa-facebook-messenger" style="margin-top: 1rem;"></i>
    </a>
    @endif
    <script type="text/javascript">
        window.addEventListener('load', function() {
            $(".widget-connect__button-activator-icon").click(function () {
                $(this).toggleClass("active");
                $(".widget-connect").toggleClass("active");
                $("a.widget-connect__button").toggleClass("button-slide-out button-slide");        
            });
        });
    </script>
</body>

</html>
