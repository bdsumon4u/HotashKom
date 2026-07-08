// Facebook Pixel + GTM dataLayer event handler
// Listens for Livewire-dispatched 'facebookEvent' browser events and:
//   1. Pushes the event to window.dataLayer (for GTM) — includes fbp/fbc for remarketing
//   2. Fires the appropriate fbq() call (standard 'track' vs custom 'trackCustom')
document.addEventListener('facebookEvent', function (event) {
    if (!event.detail || event.detail.length === 0) {
        return;
    }

    var detail = event.detail[0];
    var eventName = detail.eventName;
    var customData = detail.customData || {};
    var eventId = detail.eventId;
    var isStandard = detail.isStandard !== false;
    var tracking = detail.tracking || {};

    // Read fbp/fbc from cookies if not already sent from server
    var fbp = tracking.fbp || getCookie('_fbp') || '';
    var fbc = tracking.fbc || getCookie('_fbc') || buildFbcFromUrl() || '';

    // 1. Push to dataLayer for GTM (includes user signals for GA4/Google Ads)
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({
        event: 'meta_' + eventName,
        meta_event_name: eventName,
        meta_event_id: eventId,
        meta_event_data: customData,
        meta_fbp: fbp,
        meta_fbc: fbc,
        ecommerce: {
            currency: customData.currency || 'BDT',
            value: customData.value || 0,
            transaction_id: customData.transaction_id || customData.order_id || undefined,
            items: (customData.content_ids || []).map(function (id) {
                return {
                    item_id: id,
                    item_name: customData.content_name || '',
                    quantity: customData.quantity || 1,
                    price: customData.value || 0,
                };
            }),
        },
    });

    // 2. Fire fbq() if Meta Pixel is loaded
    if (typeof fbq !== 'function') {
        return;
    }

    if (isStandard) {
        fbq('track', eventName, customData, { eventID: eventId });
    } else {
        fbq('trackCustom', eventName, customData, { eventID: eventId });
    }

    if (window.location.hostname === 'localhost' || window.location.hostname.endsWith('.test')) {
        console.log('[Meta Pixel]', isStandard ? 'track' : 'trackCustom', eventName, {
            customData: customData,
            eventID: eventId,
            fbp: fbp,
            fbc: fbc,
        });
    }
});

// ─── Cookie / FBC helpers ───────────────────────────────────────────────────

function getCookie(name) {
    var match = document.cookie.match(new RegExp('(^|;\\s*)' + name + '=([^;]*)'));
    return match ? decodeURIComponent(match[2]) : '';
}

function buildFbcFromUrl() {
    var params = new URLSearchParams(window.location.search);
    var fbclid = params.get('fbclid');
    if (!fbclid) { return ''; }
    return 'fb.1.' + Date.now() + '.' + fbclid;
}
