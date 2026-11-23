(() => {
    if (window.orderAttribution) {
        window.orderAttribution.capture();
        return;
    }

    const storageKey = 'order_attribution_v1';
    const cookieName = 'utm_browser_data';
    const cookieDomain = document.currentScript?.dataset.cookieDomain || '';
    const domainHost = cookieDomain.replace(/^\./, '');
    const domainSuffix = domainHost && (location.hostname === domainHost || location.hostname.endsWith(`.${domainHost}`)) ? `; Domain=${cookieDomain}` : '';
    const lifetime = 30 * 24 * 60 * 60 * 1000;
    const fields = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'utm_id', 'utm_source_platform', 'fbclid', 'gclid', 'ttclid', 'msclkid', 'gbraid', 'wbraid'];
    const storage = {};
    let attribution = {};
    let entry = {};

    function decode(value) {
        try {
            const parsed = JSON.parse(value);
            return parsed && typeof parsed === 'object' && !Array.isArray(parsed) ? parsed : {};
        } catch {
            return {};
        }
    }

    function readCookie() {
        try {
            const value = document.cookie.split('; ').find((part) => part.startsWith(`${cookieName}=`));
            return value ? decode(decodeURIComponent(value.substring(cookieName.length + 1))) : {};
        } catch {
            return {};
        }
    }

    function storedData() {
        const candidates = [readCookie()];
        for (const [property, label] of [['localStorage', 'local_storage'], ['sessionStorage', 'session_storage']]) {
            try {
                const store = window[property];
                const previous = decode(store.getItem(storageKey));
                candidates.push(previous.utm || {});
                const recorded = Date.parse(previous.recorded_at || '');
                if (Number.isFinite(recorded) && Date.now() - recorded < lifetime && recorded <= Date.now() && recorded >= (Date.parse(entry.recorded_at || '') || 0)) {
                    entry = previous;
                }
                store.setItem(`${storageKey}_probe`, '1');
                store.removeItem(`${storageKey}_probe`);
                storage[label] = true;
            } catch {
                storage[label] = false;
            }
        }
        return candidates.filter((value) => {
            const captured = Date.parse(value.captured_at || '');
            return Number.isFinite(captured) && captured <= Date.now() + 300000 && Date.now() - captured < lifetime;
        }).sort((first, second) => Date.parse(second.captured_at) - Date.parse(first.captured_at))[0] || {};
    }

    function safeUrl(value) {
        if (!value) return '';
        try {
            const url = new URL(value, location.href);
            if (!['http:', 'https:'].includes(url.protocol)) return '';
            const parameters = new URLSearchParams();
            url.searchParams.forEach((entry, key) => {
                if (fields.includes(key.toLowerCase())) parameters.set(key.toLowerCase(), entry.slice(0, 200));
            });
            return `${url.origin}${url.pathname}${parameters.size ? `?${parameters}` : ''}`;
        } catch {
            return '';
        }
    }

    function capture() {
        const previous = storedData();
        const incoming = {};
        const url = new URL(location.href);
        const parameters = new URLSearchParams(url.search);
        const fragment = new URLSearchParams(url.hash.replace(/^#(?:\/?\??)?/, ''));
        for (const values of [fragment, parameters]) {
            values.forEach((value, key) => {
                key = key.toLowerCase();
                if (fields.includes(key) && value.trim()) incoming[key] = value.trim().slice(0, 200);
            });
        }

        let utm = previous;
        if (Object.keys(incoming).length) {
            const identifiers = ['utm_campaign', 'fbclid', 'gclid', 'ttclid', 'msclkid', 'gbraid', 'wbraid'];
            const conflicting = ['utm_source', ...identifiers].some((field) => incoming[field] && previous[field] && incoming[field] !== previous[field]);
            const sameCampaign = !conflicting && identifiers.some((field) => incoming[field] && incoming[field] === previous[field]);
            utm = sameCampaign ? { ...previous, ...incoming } : incoming;
            utm.captured_at = sameCampaign ? previous.captured_at : new Date().toISOString();
            utm.landing_page ||= location.pathname;
        }

        const referrer = safeUrl(document.referrer);
        let externalReferrer = false;
        try {
            externalReferrer = !!referrer && new URL(referrer).hostname.replace(/^www\./, '') !== location.hostname.replace(/^www\./, '');
        } catch {
            externalReferrer = false;
        }
        const newEntry = !entry.recorded_at || Object.keys(incoming).length > 0 || (!Object.keys(previous).length && externalReferrer);
        attribution = {
            utm,
            page_url: safeUrl(location.href),
            entry_url: newEntry ? safeUrl(location.href) : entry.entry_url,
            referrer: newEntry ? referrer : entry.referrer,
            recorded_at: newEntry ? new Date().toISOString() : entry.recorded_at,
            storage,
        };
        entry = attribution;
        const suffix = `; Path=/${domainSuffix}; SameSite=Lax${location.protocol === 'https:' ? '; Secure' : ''}`;
        try {
            document.cookie = `utm_storage_probe=1${suffix}`;
            storage.cookies = document.cookie.split('; ').includes('utm_storage_probe=1');
            document.cookie = `utm_storage_probe=; Max-Age=0${suffix}`;
            if (Object.keys(utm).length && encodeURIComponent(JSON.stringify(utm)).length < 3500) {
                document.cookie = `${cookieName}=${encodeURIComponent(JSON.stringify(utm))}; Max-Age=${Math.floor(lifetime / 1000)}${suffix}`;
            }
        } catch {
            storage.cookies = false;
        }

        for (const property of ['localStorage', 'sessionStorage']) {
            try {
                window[property].setItem(storageKey, JSON.stringify(attribution));
            } catch {
                storage[property === 'localStorage' ? 'local_storage' : 'session_storage'] = false;
            }
        }
        return attribution;
    }

    function header() {
        capture();
        const payload = { ...attribution, utm: { ...attribution.utm } };
        const encode = () => `b64.${btoa(Array.from(new TextEncoder().encode(JSON.stringify(payload)), (byte) => String.fromCharCode(byte)).join(''))}`;
        let encoded = encode();
        for (const field of ['entry_url', 'page_url', 'referrer', 'utm_term', 'utm_content', 'utm_source_platform']) {
            if (encoded.length <= 4000) break;
            payload.transport_limited = true;
            if (field.startsWith('utm_')) delete payload.utm[field];
            else delete payload[field];
            encoded = encode();
        }
        return encoded.length <= 4000 ? encoded : '';
    }

    function isCheckoutRequest(url, method) {
        return url.origin === location.origin && !['GET', 'HEAD'].includes(method.toUpperCase()) &&
            /(?:checkout|livewire\/update|save-checkout-progress)/.test(url.pathname);
    }

    const originalFetch = window.fetch;
    if (originalFetch) {
        window.fetch = function (input, options) {
            try {
                const url = new URL(typeof input === 'string' || input instanceof URL ? input : input.url, location.href);
                const method = options?.method || (input instanceof Request ? input.method : 'GET');
                if (isCheckoutRequest(url, method)) {
                    const headers = new Headers(options?.headers || (input instanceof Request ? input.headers : undefined));
                    const value = header();
                    if (value) headers.set('X-Order-Attribution', value);
                    options = { ...options, headers };
                }
            } catch {
                // Attribution must never prevent checkout from being submitted.
            }
            return originalFetch.call(this, input, options);
        };
    }

    const originalOpen = XMLHttpRequest.prototype.open;
    const originalSend = XMLHttpRequest.prototype.send;
    const requestTargets = new WeakMap();
    XMLHttpRequest.prototype.open = function (method, url, ...argumentsList) {
        try {
            requestTargets.set(this, isCheckoutRequest(new URL(url, location.href), method));
        } catch {
            requestTargets.set(this, false);
        }
        return originalOpen.call(this, method, url, ...argumentsList);
    };
    XMLHttpRequest.prototype.send = function (body) {
        if (requestTargets.get(this)) {
            try {
                const value = header();
                if (value) this.setRequestHeader('X-Order-Attribution', value);
            } catch {
                // Cookie and request-body fallbacks remain available.
            }
        }
        return originalSend.call(this, body);
    };

    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || !isCheckoutRequest(new URL(form.action, location.href), form.method)) return;
        capture();
        let input = form.querySelector('input[name="attribution"]');
        if (!input) {
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'attribution';
            form.append(input);
        }
        input.value = JSON.stringify(attribution);
    }, true);

    window.orderAttribution = { capture, payload: () => capture() };
    document.addEventListener('livewire:navigated', capture);
    capture();
})();
