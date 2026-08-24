<style>
    @php
        $color = optional($color ?? null);

        $primaryBg = $color->brand->background_color ?? '#3498DB';
        $primaryHover = $color->brand->background_hover ?? $primaryBg;
        $primaryText = $color->brand->text_color ?? '#ffffff';
        $primaryTextHover = $color->brand->text_hover ?? '#ffffff';

        if (! function_exists('hk_hex2rgb')) {
            function hk_hex2rgb($hex) {
                $hex = ltrim((string) $hex, '#');
                if (strlen($hex) === 3) {
                    $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
                }
                if (strlen($hex) !== 6) {
                    return [0, 191, 99];
                }
                return [
                    hexdec(substr($hex, 0, 2)),
                    hexdec(substr($hex, 2, 2)),
                    hexdec(substr($hex, 4, 2))
                ];
            }
        }

        if (! function_exists('hk_rgb2hex')) {
            function hk_rgb2hex($r, $g, $b) {
                return sprintf('#%02x%02x%02x', max(0, min(255, (int) $r)), max(0, min(255, (int) $g)), max(0, min(255, (int) $b)));
            }
        }

        if (! function_exists('hk_adjust_brightness')) {
            function hk_adjust_brightness($hex, $percent) {
                $rgb = hk_hex2rgb($hex);
                $r = $rgb[0] * (1 + $percent / 100);
                $g = $rgb[1] * (1 + $percent / 100);
                $b = $rgb[2] * (1 + $percent / 100);
                return hk_rgb2hex($r, $g, $b);
            }
        }

        $rgb = hk_hex2rgb($primaryBg);
        $primaryRgb = implode(', ', $rgb);
        $primaryDark = hk_adjust_brightness($primaryBg, -22);
        $primaryDarker = hk_adjust_brightness($primaryBg, -38);
        $primaryLight = 'rgba(' . $primaryRgb . ', 0.12)';
        $primarySoft = 'rgba(' . $primaryRgb . ', 0.07)';
        $primaryBorder = 'rgba(' . $primaryRgb . ', 0.22)';
    @endphp
    :root {
        --brand: {{ $primaryBg }};
        --brand-rgb: {{ $primaryRgb }};
        --brand-hover: {{ $primaryHover }};
        --brand-dark: {{ $primaryDark }};
        --brand-darker: {{ $primaryDarker }};
        --brand-light: {{ $primaryLight }};
        --brand-soft: {{ $primarySoft }};
        --brand-border: {{ $primaryBorder }};
        --brand-text: {{ $primaryText }};
        --brand-text-hover: {{ $primaryTextHover }};

        /* Universal component color tokens */
        --nm-green: var(--brand);
        --nm-green-dark: var(--brand-dark);
        --nm-green-hover: var(--brand-hover);
        --nm-green-soft: var(--brand-soft);
        --nm-green-light: var(--brand-light);
        --nm-green-border: var(--brand-border);
        --nm-category-brand: var(--brand);
        --nm-blog-green: var(--brand);
    }

    ::placeholder {
        color: #ccc !important;
    }

    .topbar,
    .site-header .topbar {
        background-color: {{ $color->topbar->background_color ?? null }} !important;
        color: {{ $color->topbar->text_color ?? null }} !important;
    }

    .topbar:hover,
    .site-header .topbar:hover {
        background-color: {{ $color->topbar->background_hover ?? null }} !important;
        color: {{ $color->topbar->text_hover ?? null }} !important;
    }

    .topbar .topbar-link,
    .site-header .topbar .topbar-link {
        color: {{ $color->topbar->text_color ?? null }} !important;
    }

    .topbar .topbar-link:hover,
    .site-header .topbar .topbar-link:hover {
        color: {{ $color->topbar->text_hover ?? null }} !important;
    }

    .site-header,
    .mobile-header__panel {
        background-color: {{ $color->header->background_color ?? null }} !important;
        color: {{ $color->header->text_color ?? null }} !important;
    }

    .site-header:hover,
    .mobile-header__panel {
        background-color: {{ $color->header->background_hover ?? null }} !important;
    }

    .site-header__phone-title,
    .site-header__phone-number {
        color: {{ $color->header->text_color ?? null }} !important;
    }

    .site-header a:hover,
    .mobile-header__panel a:hover {
        color: {{ $color->header->text_hover ?? null }} !important;
    }

    .site-header .site-header__search input {
        /* background-color: {{ $color->search->background_color ?? null }} !important; */
        color: {{ $color->search->text_color ?? null }} !important;
        border-color: {{ $color->search->text_color ?? null }} !important;
    }

    /* .site-header .site-header__search input:focus {
        background-color: {{ $color->search->background_hover ?? null }} !important;
        color: {{ $color->search->text_hover ?? null }} !important;
    } */
    .site-header .site-header__search figure {
        background-color: {{ $color->search->background_color ?? null }} !important;
        color: {{ $color->search->text_color ?? null }} !important;
        border: 2px solid {{ $color->search->text_color ?? null }} !important;
        border-left: none !important;
    }

    .site-header .site-header__search figure:hover {
        background-color: {{ $color->search->background_hover ?? null }} !important;
        color: {{ $color->search->text_hover ?? null }} !important;
    }

    .site-header .nav-panel {
        background-color: {{ $color->navbar->background_color ?? null }} !important;
    }

    .nav-links__item>a span,
    .indicator .indicator__area {
        color: {{ $color->navbar->text_color ?? null }} !important;
    }

    .mobile-header__menu-button {
        fill: {{ $color->header->text_color ?? null }} !important;
    }

    .mobile-header__indicators .indicator__area {
        color: {{ $color->header->text_color ?? null }} !important;
    }

    .nav-links__item:hover>a span,
    .indicator--trigger--click.indicator--opened .indicator__area,
    .indicator:hover .indicator__area {
        background: {{ $color->navbar->background_hover ?? null }} !important;
        color: {{ $color->navbar->text_hover ?? null }} !important;
    }

    .mobile-header__indicators .indicator--trigger--click.indicator--opened .indicator__area,
    .mobile-header__indicators .indicator:hover .indicator__area {
        background: {{ $color->header->background_hover ?? null }} !important;
        color: {{ $color->header->text_hover ?? null }} !important;
    }

    .indicator__value {
        background: {{ $color->header->background_color ?? null }} !important;
        color: {{ $color->header->text_color ?? null }} !important;
    }

    .mobile-header__indicators .indicator__value {
        background: {{ $color->navbar->background_color ?? null }} !important;
        color: {{ $color->navbar->text_color ?? null }} !important;
    }

    .departments {
        color: {{ $color->category_menu->text_color ?? null }} !important;
    }

    .departments__body {
        background: {{ $color->category_menu->background_color ?? null }} !important;
    }

    .departments__links>li:hover>a {
        background: {{ $color->category_menu->background_hover ?? null }} !important;
        color: {{ $color->category_menu->text_hover ?? null }} !important;
    }

    .departments__link-arrow,
    .departments__button-icon,
    .departments__button-arrow {
        fill: {{ $color->category_menu->text_color ?? null }} !important;
    }

    .block-header__title,
    .block-header__arrow {
        background: {{ $color->section->background_color ?? null }} !important;
        color: {{ $color->section->text_color ?? null }} !important;
    }

    .block-header__title:hover,
    .block-header__arrow:hover {
        background: {{ $color->section->background_hover ?? null }} !important;
        color: {{ $color->section->text_hover ?? null }} !important;
    }

    .block-header__title a,
    .block-header__arrow {
        color: {{ $color->section->text_color ?? null }} !important;
        fill: {{ $color->section->text_color ?? null }} !important;
    }

    .block-header__title a:hover,
    .block-header__arrow:hover {
        color: {{ $color->section->text_hover ?? null }} !important;
        fill: {{ $color->section->text_hover ?? null }} !important;
    }

    .block-header__divider {
        background: {{ $color->section->background_color ?? null }} !important;
    }

    .block-header .btn-all {
        background: {{ $color->section->background_color ?? null }} !important;
        color: {{ $color->section->text_color ?? null }} !important;
    }

    .block-header .btn-all:hover {
        background: {{ $color->section->background_hover ?? null }} !important;
        color: {{ $color->section->text_hover ?? null }} !important;
    }

    .site-footer {
        background: {{ $color->footer->background_color ?? null }} !important;
        color: {{ $color->footer->text_color ?? null }} !important;
    }

    .site-footer:hover {
        background: {{ $color->footer->background_hover ?? null }} !important;
    }

    .site-footer li:hover {
        color: {{ $color->footer->text_hover ?? null }} !important;
    }

    .product-card:before {
        box-shadow: inset 0 0 0 1px transparent !important;
    }

    .product-card:hover:before {
        box-shadow: inset 0 0 0 1px transparent !important;
    }

    .product-card__badge.product-card__badge--sale {
        background: {{ $color->badge->background_color ?? null }} !important;
        color: {{ $color->badge->text_color ?? null }} !important;
    }

    .product-card__badge.product-card__badge--sale:hover {
        background: {{ $color->badge->background_hover ?? null }} !important;
        color: {{ $color->badge->text_hover ?? null }} !important;
    }

    .page-item.active .page-link {
        background: {{ $color->primary->background_color ?? null }} !important;
        color: {{ $color->primary->text_color ?? null }} !important;
    }

    .btn-primary {
        background-color: {{ $color->primary->background_color ?? null }} !important;
        border-color: {{ $color->primary->background_color ?? null }} !important;
        color: {{ $color->primary->text_color ?? null }} !important;
    }

    .btn-primary:hover {
        background-color: {{ $color->primary->background_hover ?? null }} !important;
        border-color: {{ $color->primary->background_hover ?? null }} !important;
        color: {{ $color->primary->text_hover ?? null }} !important;
    }

    .product-card__addtocart,
    .product__addtocart {
        background-color: {{ $color->add_to_cart->background_color ?? null }} !important;
        border-color: {{ $color->add_to_cart->background_color ?? null }} !important;
        color: {{ $color->add_to_cart->text_color ?? null }} !important;
    }

    .product-card__addtocart:hover,
    .product__addtocart:hover {
        background-color: {{ $color->add_to_cart->background_hover ?? null }} !important;
        border-color: {{ $color->add_to_cart->background_hover ?? null }} !important;
        color: {{ $color->add_to_cart->text_hover ?? null }} !important;
    }

    .product-card__ordernow,
    .product__ordernow {
        background-color: {{ $color->order_now->background_color ?? null }} !important;
        border-color: {{ $color->order_now->background_color ?? null }} !important;
        color: {{ $color->order_now->text_color ?? null }} !important;
        animation: driftZoom 2s ease-in-out infinite;
    }

    .product-card__ordernow:hover,
    .product__ordernow:hover {
        background-color: {{ $color->order_now->background_hover ?? null }} !important;
        border-color: {{ $color->order_now->background_hover ?? null }} !important;
        color: {{ $color->order_now->text_hover ?? null }} !important;
        animation-play-state: paused;
    }

    .product-card__ordernow:disabled,
    .product__ordernow:disabled {
        animation: none;
    }

    @keyframes driftZoom {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(0.9);
        }
    }

    .input-radio-label__list input:checked~span,
    .input-radio-label__list input:not(:checked):not(:disabled)~span:hover {
        border-color: {{ $color->primary->background_color ?? null }} !important;
    }

    /* NehMart nav bottom border removal */
    .site-header .nav-panel {
        border-bottom: 0 !important;
        box-shadow: none !important;
    }

    .site-header .nav-panel::before,
    .site-header .nav-panel::after {
        border-bottom: 0 !important;
        box-shadow: none !important;
    }
    /* NEHMART_PREMIUM_DROPDOWN_VISUAL_ONLY */
    .nav-panel__departments .departments__button {
        background: linear-gradient(135deg, #344d64, #293f54) !important;
        border-color: rgba(255, 255, 255, .12) !important;
        box-shadow: 0 7px 16px rgba(20, 43, 60, .16);
        color: #ffffff !important;
    }

    .nav-panel__departments .departments__button:hover,
    .nav-panel__departments .departments--opened .departments__button {
        background: linear-gradient(135deg, #3b566e, #2e475e) !important;
    }

    .nav-panel__departments .departments__body {
        background: linear-gradient(180deg, #334d65 0%, #2b4258 100%) !important;
        border: 1px solid rgba(255, 255, 255, .10);
        box-shadow: 0 14px 28px rgba(22, 48, 65, .18);
    }

    .nav-panel__departments .departments__links > li > a {
        color: #eff8f2 !important;
        border-bottom-color: rgba(255, 255, 255, .09) !important;
    }

    .nav-panel__departments .departments__links > li:hover > a {
        background: linear-gradient(90deg, rgba(24, 190, 109, .26), rgba(24, 190, 109, .08)) !important;
        color: #ffffff !important;
    }

    .nav-panel__departments .departments__link-arrow,
    .nav-panel__departments .departments__button-icon,
    .nav-panel__departments .departments__button-arrow {
        fill: #dcf5e7 !important;
    }

    .nav-panel__departments .departments__item:hover .departments__link-arrow {
        fill: #ffffff !important;
    }

    .nav-panel__departments .departments__menu,
    .nav-panel__departments .menu__submenu {
        border: 1px solid #dbe9e0;
        box-shadow: 0 14px 28px rgba(22, 48, 65, .16);
    }

    .nav-panel__departments .departments__menu .menu > li:hover > a,
    .nav-panel__departments .menu__submenu .menu > li:hover > a {
        background: #edf9f1;
        color: #078b4b;
    }
</style>

    {{-- NehMart selected page top seam removal --}}
    @if (request()->is('products', 'products/*', 'contact-us', 'about-us'))
    <style>
        /*
         * Remove the thin white seam between the navigation and the first
         * content/hero block on All Products, Contact Us and About Us only.
         */
        html body .site-header + .site__body,
        html body .site__body,
        html body .site__body > :first-child,
        html body .site__body > :first-child::before,
        html body .site__body > :first-child::after {
            border-top: 0 !important;
            margin-top: 0 !important;
            box-shadow: none !important;
            outline: 0 !important;
        }

        html body .site-header + .site__body > :first-child {
            border-top-width: 0 !important;
        }
    </style>
    @endif

