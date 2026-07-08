// WhatsApp / Messenger / Tel contact tracking + WebView URL normalization
//
// For every contact link click (whatsapp, messenger, tel):
//   1. Push Contact event to window.dataLayer (GTM)
//   2. Fire fbq('track', 'Contact') if Meta Pixel is loaded
//   3. POST to /api/track-contact via sendBeacon for server-side Conversions API
//   4. Open the contact link

(function () {
    'use strict';

    if (window.__whatsappHandlersInitialized) {
        return;
    }
    window.__whatsappHandlersInitialized = true;

    // ─── Cookie / FBC helpers ───────────────────────────────────────────────────

    function getCookie(name) {
        var match = document.cookie.match(new RegExp('(^|;\\s*)' + name + '=([^;]*)'));
        return match ? decodeURIComponent(match[2]) : '';
    }

    function buildFbcFromUrl() {
        var params = new URLSearchParams(window.location.search);
        var fbclid = params.get('fbclid');
        return fbclid ? 'fb.1.' + Date.now() + '.' + fbclid : '';
    }

    // ─── dataLayer + fbq Contact event ───────────────────────────────────────

    function fireContactEvent(type, url) {
        var eventName = 'Contact';
        var eventId = 'co_' + type + '_' + Date.now();
        var eventData = { content_name: type };

        // 1. Push to dataLayer
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            event: 'meta_Contact',
            meta_event_name: eventName,
            meta_event_id: eventId,
            meta_event_data: eventData,
            contact_type: type,
            contact_url: url,
        });

        // 2. Fire fbq if available
        if (typeof fbq === 'function') {
            fbq('track', eventName, eventData, { eventID: eventId });
        }

        // 3. Server-side CAPI via sendBeacon
        try {
            var payload = JSON.stringify({
                type: type,
                url: url,
                fbp: getCookie('_fbp') || '',
                fbc: getCookie('_fbc') || buildFbcFromUrl() || '',
                event_id: eventId,
                _token: window._csrfToken,
            });
            var apiUrl = '/api/track-contact';
            if (navigator.sendBeacon) {
                var blob = new Blob([payload], { type: 'application/json' });
                navigator.sendBeacon(apiUrl, blob);
            } else {
                fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window._csrfToken || '',
                    },
                    body: payload,
                    keepalive: true,
                }).catch(function () {});
            }
        } catch (e) {
            // Silently fail
        }

        if (window.location.hostname === 'localhost' || window.location.hostname.endsWith('.test')) {
            console.log('[Contact Event]', type, url, 'ID:', eventId);
        }
    }

    // ─── WebView URL normalization ────────────────────────────────────────────

    function isWebViewEnv() {
        var ua = navigator.userAgent || navigator.vendor || window.opera;
        return /wv|WebView/i.test(ua) ||
            (window.Android !== undefined) ||
            (window.webkit && window.webkit.messageHandlers) ||
            (!window.chrome && /safari/i.test(ua));
    }

    function getWhatsAppUrlForWebView(originalUrl) {
        if (!isWebViewEnv()) {
            return originalUrl;
        }

        var ua = navigator.userAgent || navigator.vendor || window.opera;
        var isAndroid = /android/i.test(ua);
        var isFacebookOrInstagram = /FB_IAB|FBAN|Instagram/i.test(ua);
        var phoneMatch = originalUrl.match(/phone=([^&]+)/) || originalUrl.match(/wa\.me\/(\d+)/);
        var phone = phoneMatch ? phoneMatch[1] : null;

        if (!phone) {
            return originalUrl;
        }

        if (isFacebookOrInstagram) {
            return 'https://wa.me/' + phone;
        }

        if (isAndroid) {
            return 'whatsapp://send?phone=' + phone;
        }

        return originalUrl;
    }

    function getContactType(href) {
        if (!href) { return null; }
        if (href.indexOf('wa.me') !== -1 || href.indexOf('api.whatsapp.com') !== -1 || href.indexOf('whatsapp://') !== -1) {
            return 'whatsapp';
        }
        if (href.indexOf('m.me') !== -1 || href.indexOf('messenger') !== -1 || href.indexOf('facebook') !== -1) {
            return 'messenger';
        }
        if (href.indexOf('tel:') === 0) {
            return 'tel';
        }
        return null;
    }

    // ─── Global Event Delegation click listener ─────────────────────────────────

    document.addEventListener('click', function (e) {
        var anchor = e.target.closest('a');
        if (!anchor) {
            return;
        }

        var href = anchor.getAttribute('href');
        var contactType = getContactType(href) || anchor.getAttribute('data-contact-type');

        if (!contactType) {
            var whatsappUrl = anchor.getAttribute('data-whatsapp-url');
            if (whatsappUrl) {
                contactType = 'whatsapp';
            }
        }

        if (!contactType) {
            return;
        }

        // Avoid multiple processing for the same click event
        if (e.__contactTracked) {
            return;
        }
        e.__contactTracked = true;

        if (contactType === 'whatsapp') {
            var url = anchor.getAttribute('data-whatsapp-url') || href;
            if (!url) { return; }

            fireContactEvent('whatsapp', url);

            var finalUrl = getWhatsAppUrlForWebView(url);
            anchor.setAttribute('href', finalUrl);

            if (isWebViewEnv()) {
                e.preventDefault();
                e.stopImmediatePropagation();
                window.location.href = finalUrl;

                setTimeout(function () {
                    var link = document.createElement('a');
                    link.href = finalUrl;
                    link.style.display = 'none';
                    document.body.appendChild(link);
                    link.click();
                    setTimeout(function () {
                        if (link.parentNode) {
                            document.body.removeChild(link);
                        }
                    }, 100);
                }, 10);
            }
        } else {
            fireContactEvent(contactType, href || '');
        }
    }, true); // Capture phase to run before other handlers

    // ─── Helper for jQuery initialization ─────────────────────────────────────

    function runWhenJQueryReady(callback) {
        if (window.jQuery) {
            callback(window.jQuery);
        } else {
            document.addEventListener('DOMContentLoaded', function () {
                if (window.jQuery) {
                    callback(window.jQuery);
                }
            });
        }
    }

    runWhenJQueryReady(function ($) {
        $('.widget-connect__button-activator-icon')
            .off('click.widgetConnect')
            .on('click.widgetConnect', function () {
                $(this).toggleClass('active');
                $('.widget-connect').toggleClass('active');
                $('a.widget-connect__button').toggleClass('button-slide-out button-slide');
            });
    });
}());
