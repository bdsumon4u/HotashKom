@if ($metaPixel->isEnabled() || !empty(setting('pixel_ids')))
    @php
        $dbPixelIds = preg_split('/[\s\r\n,]+/', (string) setting('pixel_ids', '')) ?: [];
        $rawPixelConfig = config('meta-pixel.meta_pixel');
        $configPixelIds = collect(explode('|', (string) $rawPixelConfig))
            ->map(fn($p) => explode(':', trim($p))[0])
            ->filter()
            ->all();

        $metaPixelIds = collect(array_merge($dbPixelIds, $configPixelIds))
            ->map(fn($id) => trim($id))
            ->filter()
            ->unique()
            ->values();
    @endphp
    <!-- Meta Pixel Code -->
    <script>
        ! function(f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function() {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        @if($user = $metaPixel->getUser())
            @foreach ($metaPixelIds as $id)
                fbq('init', '{{ $id }}', {{ Js::from($user) }});
            @endforeach
        @else
            @foreach ($metaPixelIds as $id)
                fbq('init', '{{ $id }}');
            @endforeach
        @endif
    </script>
    <noscript>
        @foreach ($metaPixelIds as $id)
            <img height="1" width="1" style="display:none"
                src="https://www.facebook.com/tr?id={{ $id }}&ev=PageView&noscript=1" />
        @endforeach
    </noscript>
    <!-- End Meta Pixel Code -->
@endif
