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
                        const eventData = {{ Js::from($metaPixel['data'] ?? []) }};
                        const eventId = '{{ $metaPixel['event_id'] ?? '' }}';
                        // Use content-based hash: stable key for identical events across SPA navigation
                        const eventKey = 'fbq_' + eventName + '_' + JSON.stringify(eventData) + '_' + eventId;

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
                        const eventData = {{ Js::from($metaPixel['data'] ?? []) }};
                        const eventId = '{{ $metaPixel['event_id'] ?? '' }}';
                        // Use content-based hash: stable key for identical events across SPA navigation
                        const eventKey = 'fbq_custom_' + eventName + '_' + JSON.stringify(eventData) + '_' + eventId;

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
