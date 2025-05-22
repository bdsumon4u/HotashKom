    <!-- Scripts -->
    <script src="{{ asset('js/facebook-events.js') }}"></script>
    <script>
        // Handle Facebook events
        document.addEventListener('facebookEvent', function(event) {
            const { eventName, customData } = event.detail;
            FacebookEvents.trackEvent(eventName, customData);
        });

        // Handle meta tags
        document.addEventListener('addMetaTags', function(event) {
            const { 'user-email': email, 'user-phone': phone, 'client-ip': ip } = event.detail;

            // Remove existing meta tags
            document.querySelectorAll('meta[name^="user-"], meta[name="client-ip"]').forEach(tag => tag.remove());

            // Add new meta tags
            if (email) {
                const meta = document.createElement('meta');
                meta.name = 'user-email';
                meta.content = email;
                document.head.appendChild(meta);
            }

            if (phone) {
                const meta = document.createElement('meta');
                meta.name = 'user-phone';
                meta.content = phone;
                document.head.appendChild(meta);
            }

            if (ip) {
                const meta = document.createElement('meta');
                meta.name = 'client-ip';
                meta.content = ip;
                document.head.appendChild(meta);
            }
        });
    </script>
    @livewireScripts
