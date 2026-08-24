@extends('layouts.yellow.master')

@section('title', 'Checkout')

@section('seo_tags')
    <title>Secure Checkout | {{ $company->name ?? config('app.name') }}</title>
    <meta name="description" content="Complete your {{ $company->name ?? config('app.name') }} order securely with cash on delivery available across Bangladesh.">
    <meta name="robots" content="noindex, follow">
@endsection

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

@push('styles')
<style>
/* NEHMART_PREMIUM_CHECKOUT_LEGACY */
.checkout:not(.checkout--simple) {
    padding: 24px 0 38px;
    background: radial-gradient(circle at top right, rgba(29,191,115,.10), transparent 34%), linear-gradient(180deg,#f7fcf9,#fff 65%);
}
.checkout-page-heading { max-width:1140px; margin:0 auto 18px; padding:0 15px; }
.checkout-page-heading h1 { margin:0; color:#173c2a; font-size:clamp(24px,3vw,32px); font-weight:800; letter-spacing:-.45px; }
.checkout-page-heading h1::after { display:block; width:54px; height:4px; margin-top:9px; border-radius:99px; background:#1dbf73; content:""; }

.checkout:not(.checkout--simple) .card {
    overflow:hidden; border:1px solid #dcebe3; border-radius:14px; background:#fff;
    box-shadow:0 10px 28px rgba(22,58,39,.08);
}
.checkout:not(.checkout--simple) .card-body { padding:24px; }
.checkout:not(.checkout--simple) .form-row { padding:11px 0; border-bottom:1px solid #edf3ef; }
.checkout:not(.checkout--simple) .form-row:last-child { border-bottom:0; }
.checkout:not(.checkout--simple) .form-row label { margin-bottom:7px; color:#294536; font-size:14px; font-weight:700; }

.checkout:not(.checkout--simple) .form-control,
.checkout:not(.checkout--simple) textarea,
.checkout:not(.checkout--simple) select {
    min-height:44px; border:1px solid #cfe2d7; border-radius:9px; box-shadow:none;
    transition:border-color .2s ease, box-shadow .2s ease;
}
.checkout:not(.checkout--simple) textarea.form-control,
.checkout:not(.checkout--simple) textarea { min-height:100px; }
.checkout:not(.checkout--simple) .form-control:focus,
.checkout:not(.checkout--simple) textarea:focus,
.checkout:not(.checkout--simple) select:focus {
    border-color:#1dbf73; box-shadow:0 0 0 3px rgba(29,191,115,.14);
}
.checkout:not(.checkout--simple) .input-group-text { border-color:#cfe2d7; background:#eefaf3; color:#087c43; font-weight:800; }
.checkout:not(.checkout--simple) .form-control.h-auto { padding:13px 14px; border-radius:10px; background:#f8fdf9; }
.checkout:not(.checkout--simple) .custom-control { margin:5px 18px 5px 0; }
.checkout:not(.checkout--simple) .custom-control-label { color:#294536; font-weight:600; cursor:pointer; }
.checkout:not(.checkout--simple) .custom-control-input:checked ~ .custom-control-label::before { border-color:#1dbf73; background-color:#1dbf73; }

.checkout:not(.checkout--simple) .text-center.border.text-danger {
    margin-bottom:18px !important; padding:12px 16px !important; border:1px solid #bdebd1 !important;
    border-radius:10px; background:#effbf4; color:#087c43 !important; font-size:15px !important; font-weight:600; line-height:1.5;
}
.checkout:not(.checkout--simple) .card-title { margin-bottom:17px; color:#173c2a; font-size:20px; font-weight:800; }
.checkout:not(.checkout--simple) .checkout__totals { width:100%; margin-top:8px; margin-bottom:18px; }
.checkout:not(.checkout--simple) .checkout__totals th,
.checkout:not(.checkout--simple) .checkout__totals td {
    padding:9px 0; border-bottom:1px solid #edf3ef; color:#4b5f52; font-size:14px;
}
.checkout:not(.checkout--simple) .checkout__totals td { text-align:right; color:#173c2a; font-weight:700; }
.checkout:not(.checkout--simple) .checkout__totals-footer th,
.checkout:not(.checkout--simple) .checkout__totals-footer td {
    padding-top:14px; border-bottom:0; color:#087c43; font-size:18px !important; font-weight:800;
}

.checkout:not(.checkout--simple) .btn-outline-primary { border-color:#1dbf73; background:#fff; color:#087c43; font-weight:700; }
.checkout:not(.checkout--simple) .btn-outline-primary:hover { border-color:#1dbf73; background:#1dbf73; color:#fff; }
.checkout:not(.checkout--simple) .checkout__agree { margin:18px 0 14px; padding:12px; border-radius:9px; background:#f5fbf7; color:#405c4b; font-size:13px; }
.checkout:not(.checkout--simple) .btn-primary.btn-xl {
    min-height:54px; border:0; border-radius:10px; background:linear-gradient(135deg,#1dbf73,#0ca95d);
    box-shadow:0 9px 18px rgba(29,191,115,.24); color:#fff; font-size:17px; font-weight:800;
    transition:transform .2s ease, box-shadow .2s ease;
}
.checkout:not(.checkout--simple) .btn-primary.btn-xl:hover { transform:translateY(-1px); box-shadow:0 13px 24px rgba(29,191,115,.31); }
.checkout:not(.checkout--simple) .btn-primary.btn-xl:disabled { transform:none; opacity:.7; box-shadow:none; }

@media (min-width:768px) {
    .checkout:not(.checkout--simple) .col-md-4 > .card { position:sticky; top:18px; }
}
@media (max-width:767.98px) {
    .checkout:not(.checkout--simple) { padding:16px 0 26px; }
    .checkout-page-heading { margin-bottom:14px; }
    .checkout-page-heading h1 { font-size:24px; }
    .checkout:not(.checkout--simple) .card { border-radius:11px; }
    .checkout:not(.checkout--simple) .card-body { padding:16px; }
    .checkout:not(.checkout--simple) .form-row { padding:8px 0; }
    .checkout:not(.checkout--simple) .text-center.border.text-danger { padding:10px 12px !important; font-size:14px !important; }
    .checkout:not(.checkout--simple) .btn-primary.btn-xl { min-height:50px; font-size:16px; }
}
</style>
@endpush
@section('content')
    @php
        $checkoutTemplate = setting('show_option')->checkout_template ?? config('app.checkout_template', 'legacy');
    @endphp
    <div class="block mt-1 checkout {{ $checkoutTemplate === 'simple' ? 'checkout--simple' : '' }}">
        <div class="{{ $checkoutTemplate === 'simple' ? 'container-fluid px-lg-5' : 'container' }}">
            <header class="checkout-page-heading">
                <h1>Complete Your Order</h1>
            </header>

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
