// Facebook Pixel event handler - prevent duplicate listeners during SPA navigation
(function () {
    // Initialize tracking for custom events
    if (!window.__fbPixelCustomEventListener) {
        window.__fbPixelCustomEventListener = true;
        if (!window.__fbPixelTrackedEvents) {
            window.__fbPixelTrackedEvents = new Set();
        }

        document.addEventListener("facebookEvent", function (event) {
            if (event.detail.length === 0) {
                return;
            }

            const { eventName, customData, eventId } = event.detail[0];

            // Use main tracking store for consistency across all pixel events
            if (!window.__fbPixelTrackedEvents) {
                window.__fbPixelTrackedEvents = new Set();
            }

            // Create a unique key for this event to prevent duplicates
            const eventKey =
                "fbq_event_" +
                eventName +
                "_" +
                JSON.stringify(customData || {}) +
                "_" +
                (eventId || "");

            // Skip if this exact event was already tracked (persistent across SPA navigation)
            if (window.__fbPixelTrackedEvents.has(eventKey)) {
                console.log("Facebook Event Skipped (duplicate):", {
                    eventName,
                    customData,
                    eventID: eventId,
                });
                return;
            }

            fbq("track", eventName, customData, eventId);
            window.__fbPixelTrackedEvents.add(eventKey);

            console.log("Facebook Event Tracked:", {
                eventName,
                customData,
                eventID: eventId,
            });
        });
    }
})();
