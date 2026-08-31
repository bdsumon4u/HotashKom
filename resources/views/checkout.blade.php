@extends('layouts.yellow.master')

@section('title', 'Checkout')

@push('styles')
<style>
    .form-group {
        margin-bottom: 1rem;
    }
    .card-title {
        margin-bottom: 0.75rem;
    }
    .checkout__totals {
        margin-bottom: 10px;
    }
    .input-number .form-control:focus {
        box-shadow: none;
    }

    .checkout--simple {
        background-color: #f5f7fb;
    }

    .simple-checkout-row {
        align-items: stretch;
    }

    .simple-checkout-card,
    .simple-order-card {
        background-color: #ffffff;
        border-radius: 4px;
        box-shadow: 0 0 0 1px #e5e5e5;
        padding: 24px;
    }

    .simple-checkout-header .simple-checkout-subtitle {
        font-size: 16px;
        color: #333333;
    }

    .simple-checkout-header .simple-checkout-title {
        font-size: 22px;
        font-weight: 700;
        color: #e11b2b;
    }

    .simple-form-group {
        margin-bottom: 16px;
    }

    .simple-label {
        font-weight: 600;
        margin-bottom: 6px;
        font-size: 14px;
        color: #111111;
    }

    .simple-phone-prefix {
        min-width: 70px;
        background-color: #f0f0f0;
        border: 1px solid #d7d7d7;
        border-right: none;
        border-radius: 4px 0 0 4px;
        font-weight: 600;
    }

    .simple-shipping-options {
        display: flex;
        flex-direction: row;
        gap: 12px;
    }

    .simple-shipping-option {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        border-radius: 4px;
        border: 1px solid #d7d7d7;
        background-color: #f0f0f0;
        cursor: pointer;
        flex: 1 1 0;
    }

    .simple-shipping-option input[type="radio"] {
        margin-right: 10px;
        width: 20px;
        height: 20px;
        appearance: none;
        -webkit-appearance: none;
        border-radius: 50%;
        border: 4px solid #d9d9d9;
        background-color: #ffffff;
        position: relative;
        outline: none;
        box-sizing: border-box;
    }

    .simple-shipping-option input[type="radio"]::before {
        content: '';
        position: absolute;
        inset: 4px;
        border-radius: 50%;
        background-color: #ffffff;
    }

    .simple-shipping-option input[type="radio"]:checked {
        border-color: #c91010;
        background-color: #ffffff;
    }

    .simple-shipping-title {
        font-weight: 600;
        font-size: 14px;
    }

    .simple-terms {
        font-size: 14px;
    }

    .simple-terms-link {
        color: #007bff;
        text-decoration: underline;
    }

    .simple-submit-wrapper {
        margin-top: 8px;
    }

    .simple-submit-btn {
        background-color: #e11b2b;
        border-color: #e11b2b;
        color: #ffffff;
        font-size: 18px;
        font-weight: 700;
        padding: 14px 12px;
        border-radius: 4px;
        height: auto;
        animation: simplePulse 1.4s ease-in-out infinite alternate;
        transform-origin: center;
    }

    .simple-submit-btn:hover {
        background-color: #c60f22;
        border-color: #c60f22;
        color: #ffffff;
    }

    @keyframes simplePulse {
        0% {
            transform: scale(1);
        }

        100% {
            transform: scale(1.05);
        }
    }

    .simple-order-title {
        font-size: 22px;
        font-weight: 700;
        text-align: center;
        color: #333333;
    }

    .simple-cart-thumb img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 4px;
    }

    .simple-cart-remove {
        font-size: 16px;
    }

    .simple-qty-control .simple-qty-btn {
        background-color: #0da20d;
        color: #ffffff;
        border-radius: 0;
        padding: 4px 10px;
        line-height: 1;
    }

    .simple-qty-btn--minus {
        border-radius: 4px 0 0 4px;
    }

    .simple-qty-btn--plus {
        border-radius: 0 4px 4px 0;
    }

    .simple-qty-input {
        width: 48px;
        height: 32px;
        border: 1px solid #d7d7d7;
        border-left: none;
        border-right: none;
        border-radius: 0;
    }

    .simple-order-totals {
        border-top: 1px solid #e5e5e5;
        padding-top: 16px;
        margin-top: 8px;
    }

    .simple-total-label {
        font-size: 15px;
        font-weight: 600;
        color: #444444;
    }

    .simple-total-value {
        font-size: 16px;
        font-weight: 600;
    }

    .simple-total-value--green {
        color: #1a8f1a;
    }

    .simple-total-value--red {
        color: #ff4b2b;
    }

    .simple-total-final {
        padding-top: 8px;
        border-top: 1px solid #e5e5e5;
        margin-top: 8px;
    }

    /* ==========================================================================
       Modern Checkout Theme (Inspired by Screenshot)
       ========================================================================== */
    .checkout--modern {
        background-color: #f8fafc;
        min-height: 100vh;
        padding-top: 16px;
        padding-bottom: 40px;
    }

    .modern-checkout-container {
        max-width: 1060px;
        margin: 0 auto;
    }

    .modern-checkout-topbar {
        background-color: #ffffff;
        border-radius: 10px;
        padding: 12px 20px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    }

    .modern-brand-logo {
        max-height: 38px;
        width: auto;
        object-fit: contain;
    }

    .modern-brand-text {
        font-size: 20px;
        font-weight: 800;
        color: #1e293b;
        letter-spacing: -0.5px;
    }

    .modern-topbar-phone {
        font-size: 13.5px;
        font-weight: 600;
        color: #334155;
        text-decoration: none;
    }

    .modern-topbar-phone:hover {
        color: #0284c7;
        text-decoration: none;
    }

    .modern-icon-phone {
        color: #0284c7;
    }

    .modern-topbar-secure {
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
    }

    .modern-icon-shield {
        color: #10b981;
    }

    .modern-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        overflow: hidden;
        margin-bottom: 20px;
    }

    .modern-card-header {
        padding: 14px 18px;
        border-bottom: 1px solid #f1f5f9;
        background-color: #ffffff;
    }

    .modern-header-icon {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .modern-icon-cart,
    .modern-icon-user {
        background-color: #e0f2fe;
        color: #0284c7;
    }

    .modern-icon-summary {
        background-color: #dcfce7;
        color: #16a34a;
    }

    .modern-card-title {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
    }

    .modern-item-count {
        font-size: 13.5px;
        font-weight: 500;
        color: #64748b;
    }

    .modern-card-body {
        padding: 18px 20px;
    }

    .modern-form-group {
        margin-bottom: 16px;
    }

    .modern-label {
        font-size: 13.5px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 6px;
    }

    .modern-input {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 14px;
        color: #1e293b;
        background-color: #ffffff;
        height: auto;
        transition: all 0.2s ease;
    }

    .modern-input:focus {
        border-color: #0284c7;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
        outline: none;
    }

    .modern-textarea {
        resize: vertical;
        min-height: 80px;
    }

    .modern-textarea-sm {
        resize: vertical;
        min-height: 60px;
    }

    .modern-phone-wrapper {
        border-radius: 8px;
    }

    .modern-phone-prefix {
        background-color: #f1f5f9;
        border: 1px solid #cbd5e1;
        border-right: none;
        border-radius: 8px 0 0 8px;
        padding: 0 14px;
        font-size: 14px;
        font-weight: 600;
        color: #475569;
    }

    .modern-input-with-prefix {
        border-radius: 0 8px 8px 0 !important;
    }

    .modern-shipping-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
    }

    .modern-shipping-card {
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 14px;
        display: flex;
        align-items: center;
        cursor: pointer;
        background-color: #ffffff;
        margin-bottom: 0;
        transition: all 0.2s ease;
        user-select: none;
    }

    .modern-shipping-card:hover {
        border-color: #cbd5e1;
        background-color: #fafbfc;
    }

    .modern-shipping-card.is-selected {
        border-color: #dc2626;
        background-color: #fffaf9;
        box-shadow: 0 2px 6px rgba(220, 38, 38, 0.08);
    }

    .modern-shipping-radio {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .modern-shipping-custom-radio {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 2px solid #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #ffffff;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    .modern-shipping-card.is-selected .modern-shipping-custom-radio {
        border-color: #dc2626;
    }

    .modern-radio-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #dc2626;
        display: none;
    }

    .modern-shipping-card.is-selected .modern-radio-dot {
        display: block;
    }

    .modern-shipping-icon-badge {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background-color: #f1f5f9;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    .modern-shipping-icon-badge.icon-active {
        background-color: #fee2e2;
        color: #dc2626;
    }

    .modern-shipping-name {
        font-size: 13.5px;
        font-weight: 700;
        color: #1e293b;
    }

    .modern-shipping-cost {
        font-size: 12px;
        color: #64748b;
        margin-top: 2px;
    }

    .modern-cart-item {
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .modern-cart-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .modern-cart-item:first-child {
        padding-top: 0;
    }

    .modern-cart-thumb {
        width: 76px;
        height: 76px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        flex-shrink: 0;
        background-color: #f8fafc;
    }

    .modern-cart-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .modern-cart-name {
        font-size: 13.5px;
        font-weight: 600;
        color: #1e293b;
        line-height: 1.35;
    }

    .modern-cart-remove {
        color: #94a3b8;
        transition: color 0.15s;
    }

    .modern-cart-remove:hover {
        color: #ef4444;
    }

    .modern-cart-price {
        font-size: 13px;
        color: #64748b;
    }

    .modern-price-highlight {
        color: #dc2626;
        font-weight: 700;
    }

    .modern-qty-stepper {
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        overflow: hidden;
        background-color: #f8fafc;
    }

    .modern-qty-btn {
        width: 30px;
        height: 30px;
        border: none;
        background-color: #f1f5f9;
        color: #334155;
        font-size: 16px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        padding: 0;
        transition: background-color 0.15s;
    }

    .modern-qty-btn:hover {
        background-color: #e2e8f0;
    }

    .modern-qty-val {
        width: 36px;
        height: 30px;
        text-align: center;
        border: none;
        border-left: 1px solid #cbd5e1;
        border-right: 1px solid #cbd5e1;
        background-color: #ffffff;
        font-size: 13.5px;
        font-weight: 600;
        color: #1e293b;
        outline: none;
        -moz-appearance: textfield;
    }

    .modern-qty-val::-webkit-outer-spin-button,
    .modern-qty-val::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .modern-cart-item-total {
        font-size: 13px;
        color: #64748b;
    }

    .modern-total-highlight {
        color: #dc2626;
        font-weight: 700;
    }

    .modern-summary-row {
        font-size: 14px;
        color: #475569;
    }

    .modern-summary-value {
        color: #1e293b;
    }

    .modern-dashed-divider {
        border-top: 1px dashed #cbd5e1;
    }

    .modern-total-label {
        font-size: 15.5px;
        color: #1e293b;
    }

    .modern-total-amount {
        font-size: 18px;
        color: #dc2626;
    }

    .modern-coupon-wrapper .input-group {
        display: flex;
        align-items: stretch;
    }

    .modern-input-coupon {
        height: 38px !important;
        border-radius: 6px 0 0 6px !important;
        border: 1px solid #cbd5e1;
        border-right: none;
        font-size: 13.5px;
        padding: 6px 12px;
        box-shadow: none;
        background-color: #ffffff;
    }

    .modern-input-coupon:focus {
        border-color: #334155;
        box-shadow: none;
        outline: none;
    }

    .modern-btn-coupon {
        height: 38px !important;
        border-radius: 0 6px 6px 0 !important;
        background-color: #334155;
        border: 1px solid #334155;
        color: #ffffff;
        font-size: 13.5px;
        font-weight: 600;
        padding: 0 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
        transition: all 0.15s ease;
    }

    .modern-btn-coupon:hover {
        background-color: #1e293b;
        border-color: #1e293b;
        color: #ffffff;
    }

    .modern-terms-label {
        font-size: 13.5px;
        color: #475569;
        cursor: pointer;
    }

    .modern-terms-checkbox {
        accent-color: #0284c7;
        width: 16px;
        height: 16px;
    }

    .modern-terms-link {
        color: #0284c7;
        text-decoration: underline;
    }

    .modern-confirm-btn {
        background: linear-gradient(180deg, #dc2626 0%, #b91c1c 100%);
        border: 1px solid #b91c1c;
        color: #ffffff;
        font-size: 18px;
        font-weight: 700;
        padding: 13px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.28);
        transition: all 0.2s ease;
        animation: modernPulse 1.6s ease-in-out infinite alternate;
    }

    .modern-confirm-btn:hover {
        background: linear-gradient(180deg, #b91c1c 0%, #991b1b 100%);
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(220, 38, 38, 0.36);
    }

    .modern-lock-icon {
        color: #ffffff;
    }

    .modern-security-footer {
        font-size: 12.5px;
        color: #64748b;
    }

    .modern-lock-footer-icon {
        color: #64748b;
    }

    @keyframes modernPulse {
        0% { transform: scale(1); }
        100% { transform: scale(1.02); }
    }

    /* Responsive Mobile Ordering & Spacing */
    @media (max-width: 991.98px) {
        .modern-checkout-container {
            padding-left: 0;
            padding-right: 0;
        }

        .modern-checkout-layout {
            display: flex;
            flex-direction: column;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        .modern-col-summary {
            display: contents;
        }

        .modern-col-form {
            order: 2;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .modern-card-order-items {
            order: 1;
            margin-left: 0;
            margin-right: 0;
        }

        .modern-card-summary {
            order: 3;
            margin-left: 0;
            margin-right: 0;
        }

        .modern-checkout-actions {
            order: 4;
            margin-left: 0;
            margin-right: 0;
        }

        .modern-card-body {
            padding: 14px 14px;
        }
    }

    @media (min-width: 992px) {
        .modern-col-summary {
            position: sticky;
            top: 20px;
        }
    }
</style>
@endpush

@section('content')
    @php
        $checkoutTemplate = setting('show_option')->checkout_template ?? config('app.checkout_template', 'legacy');
    @endphp
    <div class="block mt-1 checkout {{ $checkoutTemplate === 'simple' ? 'checkout--simple' : ($checkoutTemplate === 'modern' ? 'checkout--modern' : '') }}">
        <div class="{{ $checkoutTemplate === 'simple' ? 'container-fluid px-lg-5' : ($checkoutTemplate === 'modern' ? 'container' : 'container') }}">
            <x-form checkoutform :action="route('checkout')" method="POST">
                <livewire:checkout />
            </x-form>
        </div>
    </div>
    @if (!empty($trackingDetails) && (setting('meta_pixel') || config('meta-pixel.meta_pixel') || setting('pixel_ids')))
        @php
            $jsItems = array_map(fn($p) => [
                'item_id' => $p['item_id'],
                'item_name' => $p['item_name'],
                'price' => $p['price'],
                'quantity' => $p['quantity']
            ], $trackingDetails['dataLayerItems']);
        @endphp
        <script>
            (function() {
                var eventName = 'InitiateCheckout';
                var eventId = @json($trackingDetails['event_id']);
                var eventData = @json($trackingDetails['custom_data']);

                // 1. Push to dataLayer (standard and custom events)
                window.dataLayer = window.dataLayer || [];
                window.dataLayer.push({
                    event: 'meta_' + eventName,
                    meta_event_name: eventName,
                    meta_event_id: eventId,
                    meta_event_data: eventData,
                    ecommerce: {
                        currency: 'BDT',
                        value: eventData.value,
                        items: @json($jsItems)
                    }
                });

                // 2. Fire browser fbq with identical event ID
                if (typeof fbq === 'function') {
                    fbq('track', eventName, eventData, { eventID: eventId });
                }
            })();
        </script>
    @endif
@endsection

@push('scripts')
<script>
    (function () {
        const endpoint = '/save-checkout-progress';

        const getFieldValue = (selector) => document.querySelector(selector)?.value ?? '';

        function sendCheckoutProgress() {
            const phone = getFieldValue('[name="phone"]');
            if (!phone) {
                return;
            }

            const payload = {
                name: getFieldValue('[name="name"]'),
                phone: phone,
                address: getFieldValue('[name="address"]'),
            };

            const body = JSON.stringify(payload);
            const blob = new Blob([body], { type: 'application/json' });

            if (navigator.sendBeacon) {
                navigator.sendBeacon(endpoint, blob);
            } else {
                fetch(endpoint, {
                    method: 'POST',
                    body,
                    headers: { 'Content-Type': 'application/json' },
                    keepalive: true,
                }).catch(() => {});
            }
        }

        function handlePlaceOrderClick(event) {
            const button = event.currentTarget;

            if (button.classList.contains('disabled')) {
                event.preventDefault();
                return;
            }

            button.textContent = 'Processing..';
            button.style.opacity = 1;
            button.classList.add('disabled');
        }

        function cleanupListeners() {
            if (window.__checkoutBeforeUnloadHandler) {
                window.removeEventListener('beforeunload', window.__checkoutBeforeUnloadHandler);
                window.__checkoutBeforeUnloadHandler = null;
            }
            if (window.__checkoutPageHideHandler) {
                window.removeEventListener('pagehide', window.__checkoutPageHideHandler);
                window.__checkoutPageHideHandler = null;
            }
            if (window.__checkoutVisibilityChangeHandler) {
                document.removeEventListener('visibilitychange', window.__checkoutVisibilityChangeHandler);
                window.__checkoutVisibilityChangeHandler = null;
            }
        }

        function registerCheckoutInteractions() {
            cleanupListeners();

            if (!document.querySelector('[name="phone"]')) {
                return;
            }

            window.__checkoutBeforeUnloadHandler = sendCheckoutProgress;
            window.addEventListener('beforeunload', window.__checkoutBeforeUnloadHandler, { passive: false });

            window.__checkoutPageHideHandler = sendCheckoutProgress;
            window.addEventListener('pagehide', window.__checkoutPageHideHandler);

            window.__checkoutVisibilityChangeHandler = function () {
                if (document.visibilityState === 'hidden') {
                    sendCheckoutProgress();
                }
            };
            document.addEventListener('visibilitychange', window.__checkoutVisibilityChangeHandler);

            document.querySelectorAll('[place-order]').forEach((button) => {
                if (button.__checkoutClickHandler) {
                    return;
                }

                const handler = (event) => handlePlaceOrderClick.call(button, event);
                button.addEventListener('click', handler);
                button.__checkoutClickHandler = handler;
            });
        }

        const boot = () => queueMicrotask(registerCheckoutInteractions);

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', boot, { once: true });
        } else {
            boot();
        }

        if (!window.__checkoutNavigateListenerRegistered) {
            document.addEventListener('livewire:navigate', () => {
                sendCheckoutProgress();
                cleanupListeners();
            });
            document.addEventListener('livewire:navigated', boot);
            window.__checkoutNavigateListenerRegistered = true;
        }
    })();
</script>
@endpush
