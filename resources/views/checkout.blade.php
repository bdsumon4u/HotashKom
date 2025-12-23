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

    @media (max-width: 767.98px) {
        .simple-shipping-options {
            flex-direction: column;
        }

        .simple-checkout-card,
        .simple-order-card {
            margin-bottom: 16px;
        }
    }
</style>
@endpush

@section('content')
    @php
        $checkoutTemplate = setting('show_option')->checkout_template ?? config('app.checkout_template', 'legacy');
    @endphp
    <div class="block mt-1 checkout {{ $checkoutTemplate === 'simple' ? 'checkout--simple' : '' }}">
        <div class="{{ $checkoutTemplate === 'simple' ? 'container-fluid px-lg-5' : 'container' }}">
            <x-form checkoutform :action="route('checkout')" method="POST">
                <livewire:checkout />
            </x-form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const endpoint = '/save-checkout-progress';

        const getFieldValue = (selector) => document.querySelector(selector)?.value ?? '';

        function sendCheckoutProgress() {
            const payload = {
                name: getFieldValue('[name="name"]'),
                phone: getFieldValue('[name="phone"]'),
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

        function registerCheckoutInteractions() {
            if (window.__checkoutBeforeUnloadHandler) {
                window.removeEventListener('beforeunload', window.__checkoutBeforeUnloadHandler);
            }

            // Only add beforeunload on checkout page to avoid blocking bfcache on other pages
            // Note: beforeunload must be non-passive to work, but it's only on checkout page
            window.__checkoutBeforeUnloadHandler = sendCheckoutProgress;
            window.addEventListener('beforeunload', window.__checkoutBeforeUnloadHandler, { passive: false });

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

        document.addEventListener('livewire:navigate', boot);
    })();
</script>
@endpush
