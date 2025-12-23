<style>
    @php $color =optional($color ?? null);
    @endphp
    :root {
        --primary: {{ $color->primary->background_color ?? null }};
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
            transform: scale(1.1);
        }
    }

    .input-radio-label__list input:checked~span,
    .input-radio-label__list input:not(:checked):not(:disabled)~span:hover {
        border-color: {{ $color->primary->background_color ?? null }} !important;
    }
</style>
