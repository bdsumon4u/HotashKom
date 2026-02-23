<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function() {
    // Desktop search
    var $desktopInput = $("input[name='search']").filter(function() {
        return $(this).closest('.site-header__search').length > 0;
    });
    var $desktopSuggestions = $('#desktop-search-suggestions');
    $desktopInput.on('input', function() {
        var query = $(this).val();
        if (query.length < 2) {
            $desktopSuggestions.hide();
            return;
        }
        $.get('/api/search/suggestions.json', { q: query }, function(data) {
            var html = '';
            if (data && data.length) {
                html += '<ul style="list-style:none;margin:0;padding:0">';
                data.forEach(function(item) {
                    html += '<li style="padding:8px 12px;border-bottom:1px solid #eee">';
                    html += '<a href="/products/' + item.slug + '" style="display:flex;align-items:center;text-decoration:none;color:#222">';
                    if (item.thumbnail) html += '<img src="' + item.thumbnail + '" style="width:32px;height:32px;margin-right:8px">';
                    html += '<span>' + item.name + '</span>';
                    html += '</a>';
                    html += '</li>';
                });
                html += '</ul>';
            } else {
                html = '<div style="padding:12px;color:#888">No products found</div>';
            }
            $desktopSuggestions.html(html).show();
            $desktopSuggestions.css({
                width: $desktopInput.outerWidth() || $desktopInput[0].offsetWidth,
                left: $desktopInput.position().left,
                top: $desktopInput.position().top + $desktopInput.outerHeight()
            });
        });
    });
    $desktopInput.on('blur', function() {
        setTimeout(function() { $desktopSuggestions.hide(); }, 200);
    });

    // Mobile search
    var $mobileInput = $('#bb-search-input');
    var $mobileSuggestions = $('#mobile-search-suggestions');
    $mobileInput.on('input', function() {
        var query = $(this).val();
        if (query.length < 2) {
            $mobileSuggestions.hide();
            return;
        }
        $.get('/api/search/suggestions.json', { q: query }, function(data) {
            var html = '';
            if (data && data.length) {
                html += '<ul style="list-style:none;margin:0;padding:0">';
                data.forEach(function(item) {
                    html += '<li style="padding:8px 12px;border-bottom:1px solid #eee">';
                    html += '<a href="/products/' + item.slug + '" style="display:flex;align-items:center;text-decoration:none;color:#222">';
                    if (item.thumbnail) html += '<img src="' + item.thumbnail + '" style="width:32px;height:32px;margin-right:8px">';
                    html += '<span>' + item.name + '</span>';
                    html += '</a>';
                    html += '</li>';
                });
                html += '</ul>';
            } else {
                html = '<div style="padding:12px;color:#888">No products found</div>';
            }
            $mobileSuggestions.html(html).show();
            $mobileSuggestions.css({
                width: $mobileInput.outerWidth() || $mobileInput[0].offsetWidth,
                left: $mobileInput.position().left,
                top: $mobileInput.position().top + $mobileInput.outerHeight()
            });
        });
    });
    $mobileInput.on('blur', function() {
        setTimeout(function() { $mobileSuggestions.hide(); }, 200);
    });
});
</script>
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
