{{-- Bootstrap is needed early for UI components, but can be deferred --}}
<script src="{{ cdnAsset('bootstrap.js', 'strokya/vendor/bootstrap-4.2.1/js/bootstrap.bundle.min.js') }}" crossorigin="anonymous" referrerpolicy="no-referrer" defer onerror="window.__loadLocalAsset && window.__loadLocalAsset('bootstrap')"></script>
{{-- Owl Carousel can be deferred - carousels load after initial render --}}
<script src="{{ cdnAsset('owl-carousel.js', 'strokya/vendor/owl-carousel-2.3.4/owl.carousel.min.js') }}" crossorigin="anonymous" referrerpolicy="no-referrer" defer onerror="window.__loadLocalAsset && window.__loadLocalAsset('owl')"></script>
{{-- <script src="{{ asset('strokya/vendor/nouislider-12.1.0/nouislider.min.js') }}"></script> --}}
<!-- <script src="{{ asset('strokya/js/number.js') }}"></script> -->
{{-- Main.js can be deferred - most functionality is not critical for initial render --}}
<script src="{{ versionedAsset('strokya/js/main.js') }}" defer></script>
{{-- Bootstrap notify can be deferred - notifications appear after page load --}}
<script src="{{ versionedAsset('assets/js/notify/bootstrap-notify.min.js') }}" defer></script>
<!-- Include AlgoliaSearch JS Client and autocomplete.js library -->
{{-- <script src="https://cdn.jsdelivr.net/npm/algoliasearch@3/dist/algoliasearchLite.min.js"></script>
<script src="https://cdn.jsdelivr.net/autocomplete.js/0/autocomplete.min.js"></script>
<script src="{{ asset('strokya/js/algolia.js') }}"></script>
<script src="{{ asset('strokya/vendor/jquery.bootstrap-growl.min.js') }}"></script> --}}
{{-- SVG4Everybody can be deferred - SVG fallbacks are not critical for initial render --}}
<script src="{{ cdnAsset('svg4everybody', 'strokya/vendor/svg4everybody-2.1.9/svg4everybody.min.js') }}" defer onerror="this.onerror=null;this.src='{{ asset('strokya/vendor/svg4everybody-2.1.9/svg4everybody.min.js') }}';"></script>
<script defer>
    // Wait for svg4everybody to load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof svg4everybody !== 'undefined') {
                svg4everybody();
            } else {
                window.addEventListener('load', function() {
                    if (typeof svg4everybody !== 'undefined') {
                        svg4everybody();
                    }
                });
            }
        });
    } else {
        if (typeof svg4everybody !== 'undefined') {
            svg4everybody();
        } else {
            window.addEventListener('load', function() {
                if (typeof svg4everybody !== 'undefined') {
                    svg4everybody();
                }
            });
        }
    }
</script>
