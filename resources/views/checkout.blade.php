@extends('layouts.yellow.master')

@section('title', 'Checkout')

@section('seo_tags')
    <title>Secure Checkout | {{ $company->name ?? config('app.name') }}</title>
    <meta name="description" content="Complete your {{ $company->name ?? config('app.name') }} order securely with cash on delivery available across Bangladesh.">
    <meta name="robots" content="noindex, follow">
@endsection

@section('content')
    @php
        $checkoutTemplate = setting('show_option')->checkout_template ?? config('app.checkout_template', 'legacy');
    @endphp
    <div class="block py-2 checkout-wrapper" style="background: #f8fafc;">
        <div class="container py-1">
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
