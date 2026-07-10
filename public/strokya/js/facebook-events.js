// Facebook Pixel + GTM dataLayer event handler
// Listens for Livewire-dispatched 'facebookEvent' browser events and:
//   1. Pushes the event to window.dataLayer (for GTM) — includes fbp/fbc for remarketing
//   2. Fires the appropriate fbq() call (standard 'track' vs custom 'trackCustom')
document.addEventListener('facebookEvent', function (event) {
    console.log('[Meta Pixel] facebookEvent listener triggered', event.detail);

    if (!event.detail || event.detail.length === 0) {
        console.warn('[Meta Pixel] facebookEvent triggered but event.detail is missing or empty');
        return;
    }

    var detail = event.detail[0];
    var eventName = detail.eventName;
    var customData = detail.customData || {};
    var eventId = detail.eventId;
    var isStandard = detail.isStandard !== false;
    var tracking = detail.tracking || {};
    var userData = detail.userData || {};

    console.log('[Meta Pixel] Processing event detail:', {
        eventName: eventName,
        isStandard: isStandard,
        eventId: eventId,
        customData: customData,
        userData: userData,
        tracking: tracking
    });

    // Read fbp/fbc from cookies if not already sent from server
    var fbp = tracking.fbp || getCookie('_fbp') || '';
    var fbc = tracking.fbc || getCookie('_fbc') || buildFbcFromUrl() || '';

    // Re-initialize fbq matching parameters dynamically if updated matching data is received
    var hasUserData = userData && (Array.isArray(userData) ? userData.length > 0 : Object.keys(userData).length > 0);
    if (hasUserData) {
        if (typeof fbq === 'function' && window.trackingConfig && window.trackingConfig.pixelIds && window.trackingConfig.pixelIds.length > 0) {
            window.trackingConfig.pixelIds.forEach(function (pixelId) {
                console.log('[Meta Pixel] Re-initializing fbq for pixel:', pixelId, 'with new userData:', userData);
                fbq('init', pixelId, userData);
            });
        } else {
            console.warn('[Meta Pixel] Attempted to re-initialize fbq with userData but fbq or window.trackingConfig.pixelIds is missing', {
                fbqExists: typeof fbq === 'function',
                pixelConfig: window.trackingConfig
            });
        }
    }

    // 1. Push to dataLayer for GTM (includes user signals for GA4/Google Ads)
    console.log('[Meta Pixel] Pushing to dataLayer:', 'meta_' + eventName);
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
        console.error('[Meta Pixel] fbq function is NOT defined. Browser tracking event failed to fire.');
        return;
    }

    if (isStandard) {
        console.log('[Meta Pixel] Calling fbq("track", "' + eventName + '") with eventID:', eventId);
        fbq('track', eventName, customData, { eventID: eventId });
    } else {
        console.log('[Meta Pixel] Calling fbq("trackCustom", "' + eventName + '") with eventID:', eventId);
        fbq('trackCustom', eventName, customData, { eventID: eventId });
    }

    if (window.location.hostname === 'localhost' || window.location.hostname.endsWith('.test')) {
        console.log('[Meta Pixel Debug Info]', {
            customData: customData,
            eventID: eventId,
            fbp: fbp,
            fbc: fbc,
        });
    }
});

// ─── Centralized PageView Tracking ──────────────────────────────────────────

var hasTrackedInitial = false;

function trackPageView() {
    if (typeof fbq === 'function') {
        console.log('[Meta Pixel] Firing PageView event');
        fbq('track', 'PageView');
    } else {
        console.warn('[Meta Pixel] PageView tracking skipped: fbq is not a function');
    }
}

// Track PageView on Livewire 3 SPA transitions
document.addEventListener('livewire:navigated', function () {
    console.log('[Meta Pixel] livewire:navigated event triggered');
    trackPageView();
    hasTrackedInitial = true;
});

// Fallback for non-Livewire pages or if livewire:navigated didn't fire
document.addEventListener('DOMContentLoaded', function () {
    console.log('[Meta Pixel] DOMContentLoaded event triggered. hasTrackedInitial =', hasTrackedInitial);
    setTimeout(function () {
        if (!hasTrackedInitial) {
            console.log('[Meta Pixel] DOMContentLoaded fallback executing PageView tracking');
            trackPageView();
        } else {
            console.log('[Meta Pixel] DOMContentLoaded fallback skipped: PageView already tracked');
        }
    }, 100);
});

// ─── Cookie / FBC helpers ───────────────────────────────────────────────────

function getCookie(name) {
    var match = document.cookie.match(new RegExp('(^|;\\s*)' + name + '=([^;]*)'));
    var value = match ? decodeURIComponent(match[2]) : '';
    console.log('[Meta Pixel Cookie Reader] getCookie("' + name + '") =', value ? '[found]' : '[empty]');
    return value;
}

function buildFbcFromUrl() {
    var params = new URLSearchParams(window.location.search);
    var fbclid = params.get('fbclid');
    if (!fbclid) { 
        console.log('[Meta Pixel FBC Builder] No fbclid found in URL');
        return ''; 
    }
    var fbcValue = 'fb.1.' + Date.now() + '.' + fbclid;
    console.log('[Meta Pixel FBC Builder] Constructed FBC from URL:', fbcValue);
    return fbcValue;
}
