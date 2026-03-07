@if ($metaPixel->isEnabled())
    @unless (empty($eventLayer))
        <!-- Meta Pixel Events -->
        <script id="fb-pixel-standard-events" data-fb-pixel-tracking="standard">
            (function() {
                if (!window.__fbPixelTrackedEvents) {
                    window.__fbPixelTrackedEvents = new Set();
                }

                @foreach ($eventLayer as $eventName => $metaPixel)
                    (function() {
                        const eventName = '{{ $eventName }}';
                        const eventKey = 'fbq_{{ hash('sha256', json_encode($eventLayer)) }}_' + eventName;

                        if (window.__fbPixelTrackedEvents.has(eventKey)) {
                            return;
                        }
                        window.__fbPixelTrackedEvents.add(eventKey);

                        @if (empty($metaPixel['event_id']) && empty($metaPixel['data']))
                            fbq('track', eventName);
                        @elseif (empty($metaPixel['event_id']))
                            fbq('track', eventName, {{ Js::from($metaPixel['data']) }});
                        @else
                            fbq('track', eventName, {{ Js::from($metaPixel['data']) }}, {
                                eventID: '{{ $metaPixel['event_id'] }}'
                            });
                        @endif
                    })();
                @endforeach
            })();
        </script>
        <!-- End Meta Pixel Events -->
    @endunless

    @unless (empty($customEventLayer))
        <!-- Meta Pixel Custom Events -->
        <script id="fb-pixel-custom-events" data-fb-pixel-tracking="custom">
            (function() {
                if (!window.__fbPixelTrackedEvents) {
                    window.__fbPixelTrackedEvents = new Set();
                }

                @foreach ($customEventLayer as $customEventName => $metaPixel)
                    (function() {
                        const eventName = '{{ $customEventName }}';
                        const eventKey = 'fbq_custom_{{ hash('sha256', json_encode($customEventLayer)) }}_' + eventName;

                        if (window.__fbPixelTrackedEvents.has(eventKey)) {
                            return;
                        }
                        window.__fbPixelTrackedEvents.add(eventKey);

                        @if (empty($metaPixel['event_id']) && empty($metaPixel['data']))
                            fbq('trackCustom', eventName);
                        @elseif (empty($metaPixel['event_id']))
                            fbq('trackCustom', eventName, {{ Js::from($metaPixel['data']) }});
                        @else
                            fbq('trackCustom', eventName, {{ Js::from($metaPixel['data']) }}, {
                                eventID: '{{ $metaPixel['event_id'] }}'
                            });
                        @endif
                    })();
                @endforeach
            })();
        </script>
        <!-- End Meta Custom Pixel Events -->
    @endunless
@endif
