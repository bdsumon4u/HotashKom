<link rel="stylesheet" href="{{ $bootstrapCss }}" crossorigin="anonymous" referrerpolicy="no-referrer">
{{-- Defer Owl Carousel CSS to prevent render blocking - load asynchronously --}}
@php
    $owlCarouselCss = cdnAsset('owl-carousel.css', 'strokya/vendor/owl-carousel-2.3.4/assets/owl.carousel.min.css');
@endphp
<link rel="preload" href="{{ $owlCarouselCss }}" as="style" data-async-css="true" crossorigin="anonymous" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="{{ $owlCarouselCss }}" crossorigin="anonymous"></noscript>
<link rel="stylesheet" href="{{ versionedAsset('strokya/css/style.css') }}">

<style>
    /* Modern Global Storefront Utilities */
    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji";
        background-color: #f8fafc;
        color: #0f172a;
        -webkit-font-smoothing: antialiased;
    }

    .notify-alert {
        max-width: 380px !important;
        border-radius: 6px !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
    }

    /* Modern Product Card Architecture */
    .product-card {
        position: relative;
        background-color: #ffffff;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.05);
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
        transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.2s ease;
    }

    .product-card:hover {
        transform: translateY(-4px);
        border-color: rgba(var(--brand-rgb), 0.35);
        box-shadow: 0 16px 32px -4px rgba(15, 23, 42, 0.10);
    }

    .product-card__ribbon {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 3;
    }

    .badge--free-delivery {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: linear-gradient(135deg, #059669, #047857);
        color: #ffffff;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 3px;
        line-height: 1.2;
        letter-spacing: 0.02em;
        box-shadow: 0 2px 8px rgba(5, 150, 105, 0.35);
    }

    .product-card__badges-list {
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 3;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .product-card__image {
        position: relative;
        aspect-ratio: 1 / 1;
        overflow: hidden;
        background: #f8fafc;
        border-radius: 4px 4px 0 0;
    }

    .product-card__image img {
        transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .product-card:hover .product-card__image img {
        transform: scale(1.05);
    }

    .product-card__info {
        padding: 8px 10px 0 !important;
        display: flex !important;
        flex-direction: column !important;
        flex: 0 0 auto !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }

    .product-card__name {
        margin-bottom: 2px !important;
        flex: 0 0 auto !important;
        flex-grow: 0 !important;
        min-height: auto !important;
    }

    .product-card__name a {
        color: #0f172a !important;
        font-weight: 600;
        font-size: 13.5px;
        line-height: 1.35;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: auto !important;
        transition: color 0.15s ease;
    }

    .product-card:hover .product-card__name a {
        color: var(--brand-dark) !important;
    }

    /* Dedicated Line 1: Prices - directly beneath the title */
    .product-card__prices {
        margin-top: 2px !important;
        margin-bottom: 8px !important;
        padding: 0 !important;
        display: flex !important;
        flex-direction: row !important;
        align-items: baseline !important;
        justify-content: flex-start !important;
        flex-wrap: wrap !important;
        gap: 6px !important;
        width: 100% !important;
        flex: 0 0 auto !important;
    }

    .product-card__new-price {
        font-size: 16px !important;
        font-weight: 800 !important;
        color: var(--brand-dark) !important;
        line-height: 1.2 !important;
    }

    .product-card__old-price {
        font-size: 12.5px !important;
        color: #94a3b8 !important;
        text-decoration: line-through !important;
        line-height: 1.2 !important;
    }

    /* Dedicated Line 2: Action Buttons - centered */
    .product-card__actions {
        padding: 0 10px 10px !important;
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        width: 100% !important;
        margin-top: auto !important;
        box-sizing: border-box !important;
    }

    .product-card__buttons {
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        width: 100% !important;
        margin: 0 auto !important;
    }

    .product-card__buttons .btn,
    .product-card__buttons button,
    .product-card__buttons a,
    .bb-btn-card,
    .bb-btn-card-order,
    .product-card__ordernow,
    .product-card__addtocart {
        width: 100% !important;
        max-width: 100% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
        height: 38px !important;
        min-height: 38px !important;
        border-radius: 4px !important;
        font-size: 13px !important;
        font-weight: 800 !important;
        margin: 0 auto !important;
        box-sizing: border-box !important;
    }

    .bb-modern-card {
        border: 1px solid #e2e8f0 !important;
        border-radius: 6px !important;
        background: #ffffff !important;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04) !important;
        overflow: hidden !important;
        position: relative !important;
        display: flex !important;
        flex-direction: column !important;
        height: 100% !important;
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease !important;
    }
    .bb-modern-card:hover {
        transform: translateY(-4px) !important;
        box-shadow: 0 16px 30px -4px rgba(15, 23, 42, 0.12) !important;
        border-color: var(--brand) !important;
    }
    .bb-modern-card:hover .bb-product-img {
        transform: scale(1.05) !important;
    }
    .bb-modern-card:hover .bb-product-title {
        color: var(--brand-dark) !important;
    }

    /* Strokya & grid-5-full overrides for related products and carousels */
    .product-card:before {
        display: none !important;
    }

    .products-list[data-layout^=grid-] .product-card,
    .block-products-carousel .product-card,
    .products-view__list .product-card {
        display: flex !important;
        flex-direction: column !important;
        position: relative !important;
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 6px !important;
        overflow: hidden !important;
        height: 100% !important;
    }

    .products-list[data-layout^=grid-] .product-card .product-card__image,
    .block-products-carousel .product-card .product-card__image,
    .products-view__list .product-card .product-card__image {
        padding: 0 !important;
        position: relative !important;
    }

    .products-list[data-layout^=grid-] .product-card .product-card__info,
    .block-products-carousel .product-card .product-card__info,
    .products-view__list .product-card .product-card__info {
        padding: 8px 10px 0 !important;
        display: flex !important;
        flex-direction: column !important;
        width: 100% !important;
        box-sizing: border-box !important;
        flex: 0 0 auto !important;
    }

    .products-list[data-layout^=grid-] .product-card .product-card__prices,
    .block-products-carousel .product-card .product-card__prices,
    .products-view__list .product-card .product-card__prices {
        margin-top: 2px !important;
        margin-bottom: 8px !important;
        padding: 0 !important;
        display: flex !important;
        flex-direction: row !important;
        align-items: baseline !important;
        justify-content: flex-start !important;
        flex-wrap: wrap !important;
        gap: 6px !important;
        width: 100% !important;
        flex: 0 0 auto !important;
    }

    .products-list[data-layout^=grid-] .product-card .product-card__actions,
    .block-products-carousel .product-card .product-card__actions,
    .products-view__list .product-card .product-card__actions {
        padding: 0 10px 10px !important;
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        width: 100% !important;
        margin-top: auto !important;
        position: static !important;
        box-sizing: border-box !important;
    }

    .products-list[data-layout^=grid-] .product-card .product-card__buttons,
    .block-products-carousel .product-card .product-card__buttons,
    .products-view__list .product-card .product-card__buttons {
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        width: 100% !important;
        margin: 0 auto !important;
    }

    .bb-btn-card {
        background: #ffffff !important;
        border: 1.5px solid var(--brand) !important;
        color: var(--brand-dark) !important;
        font-weight: 700 !important;
        font-size: 13px !important;
        height: 38px !important;
        min-height: 38px !important;
        border-radius: 4px !important;
        transition: all 0.2s ease !important;
    }
    .bb-btn-card:hover {
        background: var(--brand) !important;
        color: #ffffff !important;
    }

    .bb-btn-card-order {
        background: linear-gradient(135deg, var(--brand), var(--brand-dark)) !important;
        border: none !important;
        color: #ffffff !important;
        font-weight: 800 !important;
        font-size: 13px !important;
        height: 38px !important;
        min-height: 38px !important;
        border-radius: 4px !important;
        box-shadow: 0 4px 12px rgba(var(--brand-rgb), 0.25) !important;
        transition: all 0.2s ease !important;
    }
    .bb-btn-card-order:hover {
        transform: translateY(-1px) !important;
        box-shadow: 0 6px 16px rgba(var(--brand-rgb), 0.35) !important;
    }

    /* Modern Category Carousel & Showcase Headers */
    .home-category-carousel,
    .block-products-carousel {
        margin-bottom: 35px;
    }

    .block-products-carousel .block-header,
    .home-category-carousel .block-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        gap: 16px;
    }

    .block-products-carousel .block-header__title,
    .home-category-carousel .block-header__title {
        margin: 0;
        padding: 0 !important;
    }

    .block-products-carousel .block-header__title a,
    .home-category-carousel .block-header__title a {
        display: inline-flex;
        align-items: center;
        padding: 8px 18px;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #ffffff !important;
        font-size: 18px;
        font-weight: 800;
        border-radius: 5px;
        letter-spacing: -0.01em;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.15);
        text-decoration: none !important;
        transition: transform 0.2s ease, background 0.2s ease;
    }

    .block-products-carousel .block-header__title a:hover,
    .home-category-carousel .block-header__title a:hover {
        transform: translateY(-1px);
        background: linear-gradient(135deg, var(--brand-dark) 0%, var(--brand) 100%);
    }

    .block-header__divider {
        flex: 1 1 auto;
        height: 2px;
        background: linear-gradient(90deg, rgba(var(--brand-rgb), 0.3) 0%, rgba(226, 232, 240, 0.6) 100%);
        border-radius: 2px;
    }

    .block-header__arrows-list {
        display: flex;
        gap: 8px;
    }

    .block-header__arrow {
        width: 36px;
        height: 36px;
        border-radius: 5px !important;
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        color: #0f172a !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .block-header__arrow:hover {
        background: var(--brand) !important;
        border-color: var(--brand) !important;
        color: #ffffff !important;
        transform: scale(1.05);
        box-shadow: 0 4px 14px rgba(var(--brand-rgb), 0.3);
    }

    .block-header__arrow svg {
        fill: currentColor;
    }

    /* =====================================================
       PRODUCT DETAIL PAGE REDESIGN & ANIMATIONS
       ===================================================== */
    .product--layout--standard .product__buttons {
        display: flex !important;
        flex-direction: row !important;
        align-items: stretch !important;
        gap: 10px !important;
        width: 100% !important;
        margin-top: 12px !important;
    }

    .product__actions-item--ordernow,
    .product__actions-item--addtocart {
        flex: 1 1 50% !important;
        min-width: 0 !important;
        width: 50% !important;
    }

    /* Animated Order Now Button */
    .bb-btn-ordernow-animated {
        position: relative !important;
        overflow: hidden !important;
        background: linear-gradient(135deg, var(--brand), var(--brand-dark)) !important;
        border: none !important;
        color: #ffffff !important;
        font-weight: 800 !important;
        height: 48px !important;
        min-height: 48px !important;
        border-radius: 6px !important;
        font-size: 15px !important;
        box-shadow: 0 4px 14px rgba(var(--brand-rgb), 0.35) !important;
        animation: bbOrderPulse 2.4s ease-in-out infinite !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
        width: 100% !important;
        letter-spacing: 0.01em !important;
        cursor: pointer !important;
    }

    .bb-btn-ordernow-animated::after {
        content: '' !important;
        position: absolute !important;
        top: 0 !important;
        left: -120% !important;
        width: 80% !important;
        height: 100% !important;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent) !important;
        transform: skewX(-20deg) !important;
        animation: bbOrderShimmer 3s ease-in-out infinite !important;
    }

    .bb-btn-ordernow-animated:hover {
        transform: translateY(-2px) scale(1.01) !important;
        box-shadow: 0 8px 22px rgba(var(--brand-rgb), 0.45) !important;
    }

    .bb-btn-ordernow-animated i {
        animation: bbIconNudge 2.4s ease-in-out infinite !important;
    }

    @keyframes bbOrderPulse {
        0%, 100% {
            box-shadow: 0 4px 14px rgba(var(--brand-rgb), 0.35);
        }
        50% {
            box-shadow: 0 6px 20px rgba(var(--brand-rgb), 0.6), 0 0 14px rgba(var(--brand-rgb), 0.35);
        }
    }

    @keyframes bbOrderShimmer {
        0% {
            left: -120%;
        }
        28%, 100% {
            left: 140%;
        }
    }

    @keyframes bbIconNudge {
        0%, 100% {
            transform: scale(1);
        }
        15% {
            transform: scale(1.25) rotate(-10deg);
        }
        30% {
            transform: scale(1.25) rotate(10deg);
        }
        45% {
            transform: scale(1);
        }
    }

    /* Add to Cart Button (Same Line) */
    .bb-btn-addtocart-detail {
        background: #1e293b !important;
        border: none !important;
        color: #ffffff !important;
        font-weight: 800 !important;
        height: 48px !important;
        min-height: 48px !important;
        border-radius: 6px !important;
        font-size: 15px !important;
        transition: all 0.2s ease !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
        width: 100% !important;
        cursor: pointer !important;
    }

    .bb-btn-addtocart-detail:hover {
        background: #0f172a !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.25) !important;
        color: #ffffff !important;
    }

    /* Right Side Trust & Services Card Redesign */
    .bb-product-services-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        padding: 16px 14px !important;
        box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.04) !important;
    }

    .bb-services-card-header {
        font-size: 13.5px !important;
        font-weight: 800 !important;
        color: #0f172a !important;
        padding-bottom: 10px !important;
        margin-bottom: 12px !important;
        border-bottom: 1px solid #f1f5f9 !important;
        letter-spacing: -0.01em !important;
    }

    .bb-services-shield-icon {
        color: var(--brand) !important;
        font-size: 16px !important;
    }

    .bb-services-list {
        display: flex !important;
        flex-direction: column !important;
        gap: 10px !important;
    }

    .bb-service-item {
        padding: 10px 10px !important;
        border-radius: 6px !important;
        background: #f8fafc !important;
        border: 1px solid #f1f5f9 !important;
        transition: all 0.2s ease !important;
    }

    .bb-service-item:hover {
        background: #ffffff !important;
        border-color: rgba(var(--brand-rgb), 0.35) !important;
        transform: translateX(3px) !important;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06) !important;
    }

    .bb-service-icon-box {
        width: 36px !important;
        height: 36px !important;
        min-width: 36px !important;
        border-radius: 6px !important;
        background: rgba(var(--brand-rgb), 0.1) !important;
        color: var(--brand-dark) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 15px !important;
    }

    .bb-service-icon-box svg {
        width: 18px !important;
        height: 18px !important;
        fill: var(--brand-dark) !important;
        color: var(--brand-dark) !important;
    }

    .bb-service-title {
        font-size: 13px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        line-height: 1.3 !important;
        margin-bottom: 2px !important;
    }

    .bb-service-desc {
        font-size: 11.5px !important;
        line-height: 1.4 !important;
        color: #64748b !important;
    }

    /* ===================================================
       MODERN COMPACT CHECKOUT REDESIGN (EXACT MATCH)
       =================================================== */
    .bb-checkout-container {
        background-color: #f8fafc;
        padding: 10px 0 24px;
    }

    .bb-co-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 6px !important;
        box-shadow: 0 2px 10px -2px rgba(15, 23, 42, 0.04) !important;
        padding: 12px 15px !important;
        margin-bottom: 10px !important;
    }

    .bb-co-card-header {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        padding-bottom: 8px !important;
        margin-bottom: 10px !important;
        border-bottom: 1px solid #f1f5f9 !important;
    }

    .bb-co-card-icon {
        width: 26px !important;
        height: 26px !important;
        min-width: 26px !important;
        border-radius: 50% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 11.5px !important;
    }

    .bb-co-card-title {
        font-size: 14.5px !important;
        font-weight: 800 !important;
        color: #0f172a !important;
        margin: 0 !important;
        line-height: 1.2 !important;
    }

    .bb-co-form-group {
        margin-bottom: 9px !important;
    }

    .bb-co-label {
        font-size: 12.5px !important;
        font-weight: 700 !important;
        color: #1e293b !important;
        margin-bottom: 3px !important;
        display: block !important;
    }

    .bb-co-input,
    .bb-co-textarea {
        background: #ffffff !important;
        border: 1.5px solid #e2e8f0 !important;
        border-radius: 5px !important;
        padding: 6px 11px !important;
        font-size: 13px !important;
        color: #0f172a !important;
        width: 100% !important;
        min-height: 36px !important;
        transition: all 0.2s ease !important;
        box-sizing: border-box !important;
    }

    .bb-co-textarea {
        min-height: 60px !important;
    }

    .bb-co-input:focus,
    .bb-co-textarea:focus {
        border-color: var(--brand, #ca3d1c) !important;
        box-shadow: 0 0 0 3px rgba(var(--brand-rgb), 0.12) !important;
        outline: none !important;
        background: #ffffff !important;
    }

    .bb-co-phone-group {
        display: flex !important;
        align-items: stretch !important;
    }

    .bb-co-prefix {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: #f1f5f9 !important;
        border: 1.5px solid #e2e8f0 !important;
        border-right: none !important;
        border-radius: 5px 0 0 5px !important;
        padding: 0 10px !important;
        font-weight: 700 !important;
        color: #475569 !important;
        font-size: 12.5px !important;
    }

    .bb-co-phone-group .bb-co-input {
        border-radius: 0 5px 5px 0 !important;
    }

    /* Delivery Area 2-Tile Grid */
    .bb-co-delivery-grid {
        display: grid !important;
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 8px !important;
    }

    @media (max-width: 575px) {
        .bb-co-delivery-grid {
            grid-template-columns: 1fr !important;
        }
    }

    .bb-co-delivery-tile {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        padding: 7px 10px !important;
        border: 1.5px solid #e2e8f0 !important;
        border-radius: 5px !important;
        background: #ffffff !important;
        cursor: pointer !important;
        margin: 0 !important;
        transition: all 0.2s ease !important;
    }

    .bb-co-delivery-tile.is-selected {
        border-color: #dc2626 !important;
        background: #ffffff !important;
        box-shadow: 0 0 0 1px #dc2626 !important;
    }

    .bb-co-radio-circle {
        width: 16px !important;
        height: 16px !important;
        min-width: 16px !important;
        border-radius: 50% !important;
        border: 2px solid #cbd5e1 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: all 0.15s ease !important;
    }

    .bb-co-radio-circle.is-checked {
        border-color: #dc2626 !important;
    }

    .bb-co-radio-dot {
        width: 7px !important;
        height: 7px !important;
        border-radius: 50% !important;
        background: #dc2626 !important;
    }

    .bb-co-tile-icon {
        width: 26px !important;
        height: 26px !important;
        min-width: 26px !important;
        border-radius: 50% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 11.5px !important;
        background: #f1f5f9;
        color: #64748b;
    }

    .bb-co-delivery-tile.is-selected .bb-co-tile-icon {
        background: #fef2f2 !important;
        color: #dc2626 !important;
    }

    .bb-co-tile-info {
        flex: 1 1 auto !important;
        min-width: 0 !important;
    }

    .bb-co-tile-title {
        font-size: 12.5px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        line-height: 1.2 !important;
    }

    .bb-co-tile-charge {
        font-size: 11px !important;
        color: #64748b !important;
        margin-top: 1px !important;
    }

    /* Cart Items in Right Card */
    .bb-co-item-row {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding: 8px 0 !important;
        border-bottom: 1px solid #f1f5f9 !important;
        gap: 8px !important;
    }

    .bb-co-item-row:last-child {
        border-bottom: none !important;
        padding-bottom: 2px !important;
    }

    .bb-co-item-thumb {
        width: 44px !important;
        height: 44px !important;
        min-width: 44px !important;
        border-radius: 5px !important;
        border: 1px solid #e2e8f0 !important;
        overflow: hidden !important;
        background: #f8fafc !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .bb-co-item-thumb img {
        width: 100% !important;
        height: 100% !important;
        object-fit: contain !important;
    }

    .bb-co-item-details {
        flex: 1 1 auto !important;
        min-width: 0 !important;
    }

    .bb-co-item-name {
        font-size: 12.5px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        line-height: 1.25 !important;
        margin-bottom: 2px !important;
        display: -webkit-box !important;
        -webkit-line-clamp: 2 !important;
        -webkit-box-orient: vertical !important;
        overflow: hidden !important;
        text-decoration: none !important;
    }

    .bb-co-item-unit-price {
        font-size: 11.5px !important;
        color: #64748b !important;
    }

    .bb-co-item-unit-price strong {
        color: #dc2626 !important;
    }

    .bb-co-item-actions {
        display: flex !important;
        flex-direction: column !important;
        align-items: flex-end !important;
        gap: 2px !important;
        min-width: 70px !important;
    }

    .bb-co-item-remove {
        background: none !important;
        border: none !important;
        color: #94a3b8 !important;
        cursor: pointer !important;
        padding: 0 !important;
        font-size: 12px !important;
        line-height: 1 !important;
        transition: color 0.15s ease !important;
    }

    .bb-co-item-remove:hover {
        color: #ef4444 !important;
    }

    .bb-co-stepper {
        display: inline-flex !important;
        align-items: center !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 4px !important;
        overflow: hidden !important;
        background: #ffffff !important;
        height: 24px !important;
    }

    .bb-co-stepper-btn {
        width: 20px !important;
        height: 24px !important;
        border: none !important;
        background: #f8fafc !important;
        color: #475569 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 11.5px !important;
        font-weight: 700 !important;
        cursor: pointer !important;
        transition: background 0.15s ease !important;
        padding: 0 !important;
    }

    .bb-co-stepper-btn:hover {
        background: #e2e8f0 !important;
        color: #0f172a !important;
    }

    .bb-co-stepper-val {
        width: 22px !important;
        text-align: center !important;
        font-size: 11.5px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        line-height: 24px !important;
    }

    .bb-co-item-total {
        font-size: 11.5px !important;
        color: #64748b !important;
    }

    .bb-co-item-total strong {
        color: #dc2626 !important;
        font-size: 11.5px !important;
    }

    /* Order Summary Card */
    .bb-co-summary-row {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        font-size: 12.5px !important;
        margin-bottom: 6px !important;
        color: #475569 !important;
    }

    .bb-co-summary-row strong {
        color: #0f172a !important;
        font-weight: 700 !important;
    }

    .bb-co-coupon-group {
        margin: 8px 0 !important;
    }

    .bb-co-coupon-input {
        border-radius: 5px 0 0 5px !important;
        border: 1.5px solid #cbd5e1 !important;
        border-right: none !important;
        padding: 5px 10px !important;
        font-size: 12px !important;
        height: 32px !important;
    }

    .bb-co-coupon-btn {
        background: #1e293b !important;
        border: 1.5px solid #1e293b !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        border-radius: 0 5px 5px 0 !important;
        padding: 0 12px !important;
        font-size: 12px !important;
        height: 32px !important;
        transition: background 0.15s ease !important;
    }

    .bb-co-coupon-btn:hover {
        background: #0f172a !important;
    }

    .bb-co-divider {
        border-top: 1px dashed #cbd5e1 !important;
        margin: 8px 0 !important;
    }

    .bb-co-total-row {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        margin: 8px 0 !important;
    }

    .bb-co-total-label {
        font-size: 13.5px !important;
        font-weight: 800 !important;
        color: #0f172a !important;
    }

    .bb-co-total-amount {
        font-size: 18px !important;
        font-weight: 900 !important;
        color: #dc2626 !important;
    }

    .bb-btn-order-confirm {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
        border: none !important;
        color: #ffffff !important;
        font-size: 15px !important;
        font-weight: 800 !important;
        height: 42px !important;
        border-radius: 5px !important;
        box-shadow: 0 3px 12px rgba(220, 38, 38, 0.3) !important;
        transition: all 0.2s ease !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 100% !important;
        letter-spacing: -0.01em !important;
        cursor: pointer !important;
    }

    .bb-btn-order-confirm:hover {
        background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 6px 18px rgba(220, 38, 38, 0.4) !important;
        color: #ffffff !important;
    }

    .bb-co-trust-footer {
        font-size: 11px !important;
        color: #64748b !important;
        text-align: center !important;
        margin-top: 6px !important;
    }
</style>
