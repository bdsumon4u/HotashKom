@if($enabled)
    @php $id = array_merge($id, explode(' ', setting('pixel_ids'))); @endphp
    <!-- Facebook Pixel Code -->
    <script @if($nonce)nonce="{{$nonce()}}"@endif>
        // Helper function to generate event ID (matches server-side implementation)
        function generateEventId(eventName, userData, customData) {
            const data = {
                event_name: eventName,
                user_data: {
                    email: userData?.email,
                    phone: userData?.phone,
                    client_ip_address: userData?.client_ip_address
                },
                custom_data: {
                    content_ids: customData?.content_ids,
                    value: customData?.value
                },
                timestamp: Math.floor(Date.now() / 1000)
            };

            // Generate SHA-256 hash
            return btoa(JSON.stringify(data));
        }

        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        @foreach($id AS $i)
            @if($i)
            fbq('init', '{{ $i }}');
            @endif
        @endforeach
        fbq('track', 'PageView');
    </script>
    @foreach($id AS $i)
        @if($i = trim($i))
            <noscript>
                <img height="1" width="1" style="display:none"
                     src="https://www.facebook.com/tr?id={{ $i }}&ev=PageView&noscript=1"/>
            </noscript>
        @endif
    @endforeach
    <!-- End Facebook Pixel Code -->
@endif
