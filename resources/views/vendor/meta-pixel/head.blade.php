@php
    $metaPixelIds = app(\App\Services\FacebookPixelService::class)->getPixelIds();
@endphp
@if (count($metaPixelIds) > 0)
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

        window.initializedMetaPixels = window.initializedMetaPixels || {};
        @if($user = $metaPixel->getUser())
            @foreach ($metaPixelIds as $id)
                fbq('init', '{{ $id }}', {{ Js::from($user) }});
                window.initializedMetaPixels['{{ $id }}'] = true;
                window.initializedMetaPixels['{{ $id }}_userData'] = true;
            @endforeach
        @else
            @foreach ($metaPixelIds as $id)
                fbq('init', '{{ $id }}');
                window.initializedMetaPixels['{{ $id }}'] = true;
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
