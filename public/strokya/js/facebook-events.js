// Facebook Pixel event handler
document.addEventListener('facebookEvent', function(event) {
    if (event.detail.length === 0) {
        return;
    }

    const {
        eventName,
        customData,
        eventId
    } = event.detail[0];

    fbq('track', eventName, customData, eventId);

    console.log('Facebook Event Tracked:', {
        eventName,
        customData,
        eventID: eventId
    });
});

