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
        background-color: #f8fafc;
        padding: 24px 0 40px;
    }

    .simple-checkout-row {
        align-items: stretch;
    }

    .simple-checkout-card,
    .simple-order-card {
        background-color: #ffffff;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.06);
        padding: 28px;
    }

    .simple-checkout-header .simple-checkout-subtitle {
        font-size: 15px;
        color: #64748b;
        font-weight: 500;
    }

    .simple-checkout-header .simple-checkout-title {
        font-size: 20px;
        font-weight: 800;
        color: var(--brand-dark, #059669);
        letter-spacing: -0.01em;
    }

    .simple-form-group {
        margin-bottom: 18px;
    }

    .simple-label {
        font-weight: 700;
        margin-bottom: 7px;
        font-size: 14px;
        color: #0f172a;
    }

    .simple-phone-prefix {
        min-width: 65px;
        background-color: #f1f5f9;
        border: 1.5px solid #e2e8f0;
        border-right: none;
        border-radius: 4px 0 0 4px;
        font-weight: 700;
        color: #475569;
    }

    .simple-form-group input,
    .simple-form-group textarea,
    .simple-form-group select {
        border-radius: 4px !important;
        border: 1.5px solid #e2e8f0 !important;
        background: #f8fafc !important;
        padding: 10px 16px !important;
        font-size: 14px !important;
        transition: all 0.2s ease !important;
    }

    .simple-form-group input:focus,
    .simple-form-group textarea:focus,
    .simple-form-group select:focus {
        background: #ffffff !important;
        border-color: var(--brand) !important;
        box-shadow: 0 0 0 4px var(--brand-light) !important;
        outline: none !important;
    }

    .simple-shipping-options {
        display: flex;
        flex-direction: row;
        gap: 12px;
    }

    .simple-shipping-option {
        display: flex;
        align-items: center;
        padding: 14px 16px;
        border-radius: 4px;
        border: 1.5px solid #e2e8f0;
        background-color: #f8fafc;
        cursor: pointer;
        flex: 1 1 0;
        transition: all 0.2s ease;
    }

    .simple-shipping-option:hover {
        border-color: var(--brand);
        background-color: #ffffff;
    }

    .simple-shipping-option input[type="radio"] {
        margin-right: 10px;
        width: 18px;
        height: 18px;
        accent-color: var(--brand);
    }

    .simple-shipping-title {
        font-weight: 700;
        font-size: 14px;
        color: #0f172a;
    }

    .simple-terms {
        font-size: 13px;
        color: #64748b;
    }

    .simple-terms-link {
        color: var(--brand);
        font-weight: 600;
        text-decoration: underline;
    }

    .simple-submit-wrapper {
        margin-top: 14px;
    }

    .simple-submit-btn {
        background: linear-gradient(135deg, var(--brand), var(--brand-dark));
        border: none;
        color: #ffffff;
        font-size: 18px;
        font-weight: 800;
        padding: 15px 20px;
        border-radius: 5px;
        width: 100%;
        box-shadow: 0 4px 16px rgba(var(--brand-rgb), 0.3);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .simple-submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(var(--brand-rgb), 0.4);
        color: #ffffff;
    }

    .simple-order-title {
        font-size: 20px;
        font-weight: 800;
        text-align: center;
        color: #0f172a;
        margin-bottom: 20px;
    }

    .simple-cart-thumb img {
        width: 64px;
        height: 64px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #e2e8f0;
    }

    .simple-cart-remove {
        font-size: 16px;
        color: #ef4444;
        transition: opacity 0.2s ease;
    }

    .simple-qty-control .simple-qty-btn {
        background-color: var(--brand);
        color: #ffffff;
        border: none;
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
        width: 44px;
        height: 30px;
        border: 1px solid #e2e8f0;
        border-left: none;
        border-right: none;
        border-radius: 0;
        text-align: center;
        font-weight: 700;
    }

    .simple-order-totals {
        border-top: 1px solid #e2e8f0;
        padding-top: 16px;
        margin-top: 14px;
    }

    .simple-total-label {
        font-size: 14px;
        font-weight: 600;
        color: #64748b;
    }

    .simple-total-value {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
    }

    .simple-total-value--green {
        color: var(--brand-dark);
    }

    .simple-total-value--red {
        color: #ef4444;
    }

    .simple-total-final {
        padding-top: 12px;
        border-top: 1px solid #e2e8f0;
        margin-top: 10px;
    }

    .simple-total-final .simple-total-value {
        font-size: 20px;
        font-weight: 800;
        color: var(--brand-dark);
    }

    @media (max-width: 767.98px) {
        .simple-shipping-options {
            flex-direction: column;
        }

        .simple-checkout-card,
        .simple-order-card {
            padding: 20px;
            margin-bottom: 16px;
            border-radius: 14px;
        }
    }
</style>
@endpush

@push('styles')
<style>
/* BAGBAZARBD_PREMIUM_CHECKOUT_LEGACY */
.checkout:not(.checkout--simple) {
    padding: 24px 0 40px;
    background: #f8fafc;
}
.checkout-page-heading { max-width:1140px; margin:0 auto 20px; padding:0 15px; }
.checkout-page-heading h1 { margin:0; color:#0f172a; font-size:clamp(24px,3vw,30px); font-weight:800; letter-spacing:-.45px; }
.checkout-page-heading h1::after { display:block; width:48px; height:3px; margin-top:8px; border-radius:99px; background:var(--brand); content:""; }

.checkout:not(.checkout--simple) .card {
    overflow:hidden; border:1px solid #e2e8f0; border-radius:16px; background:#fff;
    box-shadow:0 4px 20px -2px rgba(15,23,42,.06);
}
.checkout:not(.checkout--simple) .card-body { padding:28px; }
.checkout:not(.checkout--simple) .form-row { padding:10px 0; border-bottom:1px solid #f1f5f9; }
.checkout:not(.checkout--simple) .form-row:last-child { border-bottom:0; }
.checkout:not(.checkout--simple) .form-row label { margin-bottom:7px; color:#0f172a; font-size:14px; font-weight:700; }

.checkout:not(.checkout--simple) .form-control,
.checkout:not(.checkout--simple) textarea,
.checkout:not(.checkout--simple) select {
    min-height:44px; border:1.5px solid #e2e8f0; border-radius:10px; box-shadow:none;
    background:#f8fafc; transition:all .2s ease;
}
.checkout:not(.checkout--simple) textarea.form-control,
.checkout:not(.checkout--simple) textarea { min-height:100px; }
.checkout:not(.checkout--simple) .form-control:focus,
.checkout:not(.checkout--simple) textarea:focus,
.checkout:not(.checkout--simple) select:focus {
    background:#fff; border-color:var(--brand); box-shadow:0 0 0 4px var(--brand-light);
}
.checkout:not(.checkout--simple) .input-group-text { border-color:#e2e8f0; background:#f1f5f9; color:var(--brand-dark); font-weight:800; border-radius:10px 0 0 10px; }

.checkout:not(.checkout--simple) .card-title { margin-bottom:18px; color:#0f172a; font-size:18px; font-weight:800; }
.checkout:not(.checkout--simple) .checkout__totals { width:100%; margin-top:8px; margin-bottom:18px; }
.checkout:not(.checkout--simple) .checkout__totals th,
.checkout:not(.checkout--simple) .checkout__totals td {
    padding:10px 0; border-bottom:1px solid #f1f5f9; color:#64748b; font-size:14px;
}
.checkout:not(.checkout--simple) .checkout__totals td { text-align:right; color:#0f172a; font-weight:700; }
.checkout:not(.checkout--simple) .checkout__totals-footer th,
.checkout:not(.checkout--simple) .checkout__totals-footer td {
    padding-top:14px; border-bottom:0; color:var(--brand-dark); font-size:18px !important; font-weight:800;
}

.checkout:not(.checkout--simple) .btn-primary.btn-xl {
    min-height:50px; border:0; border-radius:10px; background:linear-gradient(135deg,var(--brand),var(--brand-dark));
    box-shadow:0 4px 14px rgba(var(--brand-rgb),.25); color:#fff; font-size:17px; font-weight:800;
    transition:transform .2s ease, box-shadow .2s ease;
}
.checkout:not(.checkout--simple) .btn-primary.btn-xl:hover { transform:translateY(-1px); box-shadow:0 8px 20px rgba(var(--brand-rgb),.35); }

@media (min-width:768px) {
    .checkout:not(.checkout--simple) .col-md-4 > .card { position:sticky; top:18px; }
}
@media (max-width:767.98px) {
    .checkout:not(.checkout--simple) { padding:16px 0 26px; }
    .checkout:not(.checkout--simple) .card-body { padding:18px; }
}
</style>
@endpush
@section('content')
    @php
        $checkoutTemplate = setting('show_option')->checkout_template ?? config('app.checkout_template', 'legacy');
    @endphp
    <div class="block py-2 checkout-wrapper" style="background: #f8fafc;">
        <div class="container py-1">
            <x-form checkoutform :action="route('checkout')" method="POST">
                @if ($checkoutTemplate === 'simple')
                    <livewire:checkout-simple />
                @else
                    <livewire:checkout />
                @endif
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
