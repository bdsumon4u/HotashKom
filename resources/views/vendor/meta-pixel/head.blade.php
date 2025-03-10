@if($metaPixel->isEnabled())
    <!-- Meta Pixel Code -->
    <script>
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
        @foreach(explode(' ', setting('pixel_ids')) as $id)
            @if($id)
                fbq('init', '{{ $id }}');
            @endif
        @endforeach
    @endif
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ $metaPixel->pixelId() }}&ev=PageView&noscript=1"/>
    </noscript>
    <!-- End Meta Pixel Code -->
@endif
