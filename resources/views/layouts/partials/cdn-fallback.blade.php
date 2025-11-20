<script data-navigate-once>
    (function () {
        if (window.__cdnFallbackInitialized) {
            return;
        }

        window.__cdnFallbackInitialized = true;
        window.__cdnFallbackAssets = window.__cdnFallbackAssets || {};

        window.__registerCdnFallbacks = function (map) {
            if (! map) {
                return;
            }

            window.__cdnFallbackAssets = Object.assign(window.__cdnFallbackAssets, map);
        };

        window.__loadLocalAsset = function (key) {
            if (! key || ! window.__cdnFallbackAssets[key]) {
                return;
            }

            if (document.querySelector('script[data-local-'+key+']')) {
                return;
            }

            const script = document.createElement('script');
            script.src = window.__cdnFallbackAssets[key];
            script.dataset.navigateOnce = 'true';
            script.setAttribute('data-local-'+key, 'true');
            script.addEventListener('load', function () {
                if (typeof window.__flushRunWhenJQueryQueue === 'function') {
                    window.__flushRunWhenJQueryQueue();
                }
            });
            document.head.appendChild(script);
        };
    })();
</script>
@if (! empty($fallbackAssets ?? []))
    <script data-navigate-once>
        window.__registerCdnFallbacks && window.__registerCdnFallbacks(@json($fallbackAssets));
    </script>
@endif

