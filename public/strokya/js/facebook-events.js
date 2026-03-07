// Facebook Pixel event handler - prevent duplicate listeners during SPA navigation
(function () {
    // Initialize tracking for custom events
    if (!window.__fbPixelCustomEventListener) {
        window.__fbPixelCustomEventListener = true;
        window.__fbPixelTrackedCustomEvents = new Set();

        document.addEventListener("facebookEvent", function (event) {
            if (event.detail.length === 0) {
                return;
            }

            const { eventName, customData, eventId } = event.detail[0];

            // Create a unique key for this event to prevent duplicates
            const eventKey =
                "fbq_custom_" +
                eventName +
                "_" +
                JSON.stringify(customData || {});

            // Skip if this exact event was already tracked recently (within current navigation cycle)
            if (window.__fbPixelTrackedCustomEvents.has(eventKey)) {
                console.log("Facebook Event Skipped (duplicate):", {
                    eventName,
                    customData,
                    eventID: eventId,
                });
                return;
            }

            fbq("track", eventName, customData, eventId);
            window.__fbPixelTrackedCustomEvents.add(eventKey);

            // Clear tracked events after a delay to allow for new navigation cycles
            setTimeout(function () {
                window.__fbPixelTrackedCustomEvents.clear();
            }, 1000);

            console.log("Facebook Event Tracked:", {
                eventName,
                customData,
                eventID: eventId,
            });
        });
    }
})();
