// Facebook Events Helper
const FacebookEvents = {
    /**
     * Track an event with event ID for deduplication
     * @param {string} eventName - The name of the event
     * @param {Object} customData - Custom data for the event
     * @param {Object} userData - User data for matching
     */
    trackEvent: function(eventName, customData = {}, userData = {}) {
        // Get user data from meta tags or data attributes
        const userDataFromPage = {
            email: document.querySelector('meta[name="user-email"]')?.content,
            phone: document.querySelector('meta[name="user-phone"]')?.content,
            client_ip_address: document.querySelector('meta[name="client-ip"]')?.content
        };

        // Merge provided user data with page data
        const mergedUserData = { ...userDataFromPage, ...userData };

        // Generate event ID
        const eventId = generateEventId(eventName, mergedUserData, customData);

        // Track event with event ID
        fbq('track', eventName, {
            ...customData,
            event_id: eventId
        });

        // Log event for debugging
        console.log('Facebook Event Tracked:', {
            eventName,
            eventId,
            customData,
            userData: mergedUserData
        });
    },

    /**
     * Track AddToCart event
     * @param {Object} product - Product data
     */
    trackAddToCart: function(product) {
        this.trackEvent('AddToCart', {
            currency: 'BDT',
            value: product.price,
            content_ids: [product.id],
            content_name: product.name
        });
    },

    /**
     * Track Purchase event
     * @param {Object} order - Order data
     * @param {Array} products - Array of purchased products
     */
    trackPurchase: function(order, products) {
        this.trackEvent('Purchase', {
            currency: 'BDT',
            value: order.total,
            content_ids: products.map(p => p.id),
            content_name: 'Purchase',
            transaction_id: order.id
        });
    }
};
