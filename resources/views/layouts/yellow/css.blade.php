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
        margin-right: 8px !important;
    }

    .bb-services-list {
        display: flex !important;
        flex-direction: column !important;
        gap: 8px !important;
    }

    .bb-service-item {
        padding: 10px 12px !important;
        border-radius: 6px !important;
        background: #f8fafc !important;
        border: 1px solid #f1f5f9 !important;
        margin-bottom: 8px !important;
        transition: all 0.2s ease !important;
    }

    .bb-service-item:last-child {
        margin-bottom: 0 !important;
    }

    .bb-service-item:hover {
        background: #ffffff !important;
        border-color: rgba(var(--brand-rgb), 0.35) !important;
        transform: translateX(3px) !important;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06) !important;
    }

    .bb-service-icon-box {
        width: 38px !important;
        height: 38px !important;
        min-width: 38px !important;
        border-radius: 6px !important;
        background: rgba(var(--brand-rgb), 0.1) !important;
        color: var(--brand-dark) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 15px !important;
        margin-right: 12px !important;
        flex-shrink: 0 !important;
        margin-top: 1px !important;
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
       ASELLBD STYLE CLEAN CHECKOUT REDESIGN
       =================================================== */
    .checkout-wrapper {
        background-color: #f8fafc !important;
        padding: 16px 0 32px !important;
    }

    .checkout-wrapper .container {
        max-width: 1040px !important;
        padding-left: 12px !important;
        padding-right: 12px !important;
    }

    .asell-checkout-wrap {
        width: 100%;
    }

    .asell-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 4px !important;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04) !important;
    }

    .asell-form-card {
        padding: 16px 20px !important;
    }

    /* Red Notice Banner */
    .asell-notice-banner {
        border: 1px dashed #ef4444 !important;
        background: #fff5f5 !important;
        color: #dc2626 !important;
        font-size: 14px !important;
        font-weight: 700 !important;
        text-align: center !important;
        padding: 8px 14px !important;
        border-radius: 4px !important;
        margin-bottom: 16px !important;
        line-height: 1.4 !important;
    }

    .asell-notice-highlight {
        color: #b91c1c !important;
        font-weight: 800 !important;
    }

    /* Horizontal Form Rows */
    .asell-form-row {
        display: flex !important;
        align-items: flex-start !important;
        margin-bottom: 11px !important;
    }

    .asell-form-label {
        width: 135px !important;
        min-width: 135px !important;
        font-size: 13.5px !important;
        font-weight: 700 !important;
        color: #1e293b !important;
        padding-top: 7px !important;
        margin-bottom: 0 !important;
    }

    .asell-form-field {
        flex: 1 1 auto !important;
        min-width: 0 !important;
    }

    .asell-input,
    .asell-textarea,
    .asell-select {
        background: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 4px !important;
        padding: 7px 12px !important;
        font-size: 13.5px !important;
        color: #1e293b !important;
        width: 100% !important;
        min-height: 38px !important;
        transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
        box-sizing: border-box !important;
    }

    .asell-textarea {
        min-height: 60px !important;
    }

    .asell-input:focus,
    .asell-textarea:focus,
    .asell-select:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
        outline: none !important;
        background: #ffffff !important;
    }

    .asell-phone-group {
        display: flex !important;
        align-items: stretch !important;
    }

    .asell-prefix {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: #f1f5f9 !important;
        border: 1px solid #cbd5e1 !important;
        border-right: none !important;
        border-radius: 4px 0 0 4px !important;
        padding: 0 10px !important;
        font-weight: 700 !important;
        color: #475569 !important;
        font-size: 13px !important;
    }

    .asell-phone-group .asell-input {
        border-radius: 0 4px 4px 0 !important;
    }

    /* Radio Shipping Options */
    .asell-shipping-options {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 16px !important;
        padding-top: 7px !important;
    }

    .asell-radio-label {
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        cursor: pointer !important;
        font-size: 13.5px !important;
        color: #1e293b !important;
        font-weight: 600 !important;
        margin-bottom: 0 !important;
    }

    .asell-radio-input {
        accent-color: #2563eb !important;
        width: 16px !important;
        height: 16px !important;
        margin: 0 !important;
        cursor: pointer !important;
    }

    /* Right Column - Summary Card */
    .asell-summary-card {
        padding: 20px 22px !important;
    }

    .asell-summary-heading {
        font-size: 22px !important;
        font-weight: 800 !important;
        color: #1e293b !important;
        margin-bottom: 16px !important;
        letter-spacing: -0.01em !important;
    }

    /* Coupon Box */
    .asell-coupon-box {
        margin-bottom: 16px !important;
    }

    .asell-coupon-label {
        font-size: 13px !important;
        font-weight: 600 !important;
        color: #475569 !important;
        margin-bottom: 5px !important;
        display: block !important;
    }

    .asell-coupon-input-group {
        display: flex !important;
        align-items: stretch !important;
    }

    .asell-coupon-input {
        border: 1px solid #cbd5e1 !important;
        border-right: none !important;
        border-radius: 4px 0 0 4px !important;
        padding: 7px 12px !important;
        font-size: 13px !important;
        flex: 1 1 auto !important;
        min-height: 38px !important;
        outline: none !important;
    }

    .asell-coupon-input:focus {
        border-color: #3b82f6 !important;
    }

    .asell-coupon-btn {
        border: 1px solid #3b82f6 !important;
        background: #ffffff !important;
        color: #2563eb !important;
        font-weight: 700 !important;
        font-size: 13px !important;
        padding: 0 18px !important;
        border-radius: 0 4px 4px 0 !important;
        cursor: pointer !important;
        transition: all 0.15s ease !important;
    }

    .asell-coupon-btn:hover {
        background: #2563eb !important;
        color: #ffffff !important;
    }

    /* Summary Lines */
    .asell-summary-rows {
        margin-top: 14px !important;
        border-top: 1px solid #f1f5f9 !important;
        padding-top: 14px !important;
    }

    .asell-summary-line {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        margin-bottom: 10px !important;
        font-size: 14px !important;
    }

    .asell-line-label {
        color: #334155 !important;
        font-weight: 700 !important;
    }

    .asell-line-value {
        color: #0f172a !important;
        font-weight: 700 !important;
    }

    .asell-total-line {
        margin-top: 14px !important;
        padding-top: 12px !important;
        border-top: 1px solid #f1f5f9 !important;
        margin-bottom: 14px !important;
    }

    .asell-total-label {
        font-size: 15px !important;
        font-weight: 800 !important;
        color: #0f172a !important;
    }

    .asell-total-val {
        font-size: 17px !important;
        font-weight: 800 !important;
        color: #0f172a !important;
    }

    /* Terms */
    .asell-terms-wrap {
        margin-bottom: 16px !important;
    }

    .asell-terms-label {
        font-size: 12.5px !important;
        color: #475569 !important;
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
        cursor: pointer !important;
        margin-bottom: 0 !important;
    }

    .asell-terms-cb {
        accent-color: #2563eb !important;
        width: 15px !important;
        height: 15px !important;
        margin: 0 !important;
    }

    .asell-terms-link {
        color: #0284c7 !important;
        text-decoration: underline !important;
        background: none !important;
        border: none !important;
        padding: 0 !important;
        font: inherit !important;
        cursor: pointer !important;
    }

    /* Confirm Order CTA */
    .asell-btn-confirm {
        width: 100% !important;
        background: #2f435a !important;
        color: #ffffff !important;
        font-size: 16px !important;
        font-weight: 800 !important;
        padding: 12px 16px !important;
        border-radius: 4px !important;
        border: none !important;
        cursor: pointer !important;
        transition: background 0.15s ease, transform 0.1s ease !important;
        box-shadow: 0 2px 6px rgba(47, 67, 90, 0.25) !important;
        text-align: center !important;
    }

    .asell-btn-confirm:hover {
        background: #243447 !important;
        transform: translateY(-1px) !important;
        color: #ffffff !important;
    }

    .asell-trust-badge {
        text-align: center !important;
        margin-top: 10px !important;
        font-size: 12px !important;
        color: #64748b !important;
    }

    /* Bottom Section: Product Overview */
    .asell-product-overview-wrap {
        margin-top: 20px !important;
    }

    .asell-overview-heading {
        font-size: 18px !important;
        font-weight: 800 !important;
        color: #1e293b !important;
        margin-bottom: 10px !important;
        letter-spacing: -0.01em !important;
    }

    .asell-overview-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 4px !important;
        overflow: hidden !important;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04) !important;
    }

    .asell-product-table thead th {
        background: #f8fafc !important;
        color: #475569 !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        border-bottom: 1px solid #e2e8f0 !important;
        border-top: none !important;
        padding: 10px 14px !important;
    }

    .asell-product-table tbody td {
        vertical-align: middle !important;
        padding: 12px 14px !important;
        border-top: 1px solid #f1f5f9 !important;
        font-size: 13.5px !important;
    }

    .asell-item-img {
        width: 48px !important;
        height: 48px !important;
        object-fit: cover !important;
        border-radius: 4px !important;
        border: 1px solid #e2e8f0 !important;
    }

    .asell-item-title {
        font-weight: 700 !important;
        color: #1e293b !important;
        text-decoration: none !important;
        font-size: 13.5px !important;
        line-height: 1.35 !important;
        display: -webkit-box !important;
        -webkit-line-clamp: 2 !important;
        -webkit-box-orient: vertical !important;
        overflow: hidden !important;
    }

    .asell-item-title:hover {
        color: #2563eb !important;
    }

    .asell-price-cell {
        font-weight: 600 !important;
        color: #334155 !important;
    }

    .asell-stepper {
        display: inline-flex !important;
        align-items: center !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 4px !important;
        overflow: hidden !important;
    }

    .asell-stepper-btn {
        background: #f8fafc !important;
        border: none !important;
        width: 26px !important;
        height: 26px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 14px !important;
        font-weight: 700 !important;
        color: #334155 !important;
        cursor: pointer !important;
        padding: 0 !important;
        transition: background 0.15s ease !important;
    }

    .asell-stepper-btn:hover {
        background: #e2e8f0 !important;
    }

    .asell-stepper-val {
        min-width: 28px !important;
        text-align: center !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
    }

    .asell-subtotal-cell strong {
        color: #0f172a !important;
        font-weight: 800 !important;
    }

    .asell-remove-btn {
        background: none !important;
        border: none !important;
        color: #94a3b8 !important;
        font-size: 15px !important;
        cursor: pointer !important;
        padding: 4px 8px !important;
        transition: color 0.15s ease !important;
    }

    .asell-remove-btn:hover {
        color: #ef4444 !important;
    }

    @media (max-width: 575.98px) {
        .asell-form-row {
            display: block !important;
            margin-bottom: 12px !important;
        }

        .asell-form-label {
            display: block !important;
            width: 100% !important;
            padding-top: 0 !important;
            margin-bottom: 4px !important;
        }

        .asell-form-card,
        .asell-summary-card {
            padding: 14px 14px !important;
        }
    }

    .site-footer__widgets {
        padding: 0 !important;
    }
</style>
