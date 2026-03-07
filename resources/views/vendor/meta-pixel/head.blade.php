@if($metaPixel->isEnabled())
    <!-- Meta Pixel Code - Deferred to reduce main-thread blocking -->
    <script>
        // Defer Facebook Pixel loading until after page is interactive
        (function() {
            function loadFacebookPixel() {
                if (window.fbq) return;
                !function(f,b,e,v,n,t,s)
                {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                    n.queue=[];t=b.createElement(e);t.async=!0;
                    t.src=v;s=b.getElementsByTagName(e)[0];
                    s.parentNode.insertBefore(t,s)}(window, document,'script',
                    'https://connect.facebook.net/en_US/fbevents.js');
    @if(false)
        @if($user = $metaPixel->getUser())
            @if($userIdAsString)
                fbq('init', '{{ $metaPixel->pixelId() }}', {em: '{{ $user['em'] }}', external_id: '{{ $user['external_id'] }}'});
            @else
                fbq('init', '{{ $metaPixel->pixelId() }}', {em: '{{ $user['em'] }}', external_id: {{ $user['external_id'] }}});
            @endif
        @else
            fbq('init', '{{ $metaPixel->pixelId() }}');
        @endif
    @else
        @foreach(explode(' ', $metaPixel->pixelId()) as $id)
            @if($id)
                fbq('init', '{{ $id }}');
            @endif
        @endforeach
    @endif
                fbq('track', 'PageView');
            }

            // Load after page is interactive (requestIdleCallback or setTimeout fallback)
            if ('requestIdleCallback' in window) {
                requestIdleCallback(loadFacebookPixel, { timeout: 2000 });
            } else {
                setTimeout(loadFacebookPixel, 2000);
            }
        })();
    </script>
    <noscript>
        <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ $metaPixel->pixelId() }}&ev=PageView&noscript=1"/>
    </noscript>
    <!-- End Meta Pixel Code -->
@endif
