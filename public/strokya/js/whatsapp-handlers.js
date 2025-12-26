// WhatsApp and widget connect handlers
runWhenJQueryReady(function($) {
    $(".widget-connect__button-activator-icon")
        .off('click.widgetConnect')
        .on('click.widgetConnect', function() {
            $(this).toggleClass("active");
            $(".widget-connect").toggleClass("active");
            $("a.widget-connect__button").toggleClass("button-slide-out button-slide");
        });

    function isWebView() {
        var ua = navigator.userAgent || navigator.vendor || window.opera;
        return /wv|WebView/i.test(ua) ||
            (window.Android !== undefined) ||
            (window.webkit && window.webkit.messageHandlers) ||
            !window.chrome;
    }

    function normalizeWhatsAppUrl(originalUrl) {
        if (!isWebView()) {
            return originalUrl;
        }

        var ua = navigator.userAgent || navigator.vendor || window.opera;
        var isAndroid = /android/i.test(ua);
        var isFacebookOrInstagram = /FB_IAB|FBAN|Instagram/i.test(ua);

        var phoneMatch = originalUrl.match(/phone=([^&]+)/) || originalUrl.match(/wa\.me\/(\d+)/);
        var phone = phoneMatch ? phoneMatch[1] : null;

        if (!phone) {
            return originalUrl;
        }

        if (isFacebookOrInstagram) {
            return 'https://wa.me/' + phone;
        }

        if (isAndroid) {
            return 'whatsapp://send?phone=' + phone;
        }

        return originalUrl;
    }

    function handleWhatsAppClick(e, element) {
        var url = $(element).data('whatsapp-url') || $(element).attr('href');
        if (!url) return true;

        var finalUrl = normalizeWhatsAppUrl(url);
        $(element).attr('href', finalUrl);

        if (isWebView()) {
            e.preventDefault();
            e.stopImmediatePropagation();
            window.location.href = finalUrl;

            setTimeout(function() {
                var link = document.createElement('a');
                link.href = finalUrl;
                link.style.display = 'none';
                document.body.appendChild(link);
                link.click();
                setTimeout(function() {
                    if (link.parentNode) {
                        document.body.removeChild(link);
                    }
                }, 100);
            }, 10);

            return false;
        }

        return true;
    }

    $(".widget-connect__button-whatsapp")
        .off('click.whatsapp')
        .on('click.whatsapp', function(e) {
            return handleWhatsAppClick(e, this);
        });
});

function isWebViewEnv() {
    var ua = navigator.userAgent || navigator.vendor || window.opera;
    return /wv|WebView/i.test(ua) ||
        (window.Android !== undefined) ||
        (window.webkit && window.webkit.messageHandlers) ||
        (!window.chrome && /safari/i.test(ua));
}

function getWhatsAppUrlForWebView(originalUrl) {
    if (!isWebViewEnv()) {
        return originalUrl;
    }

    var ua = navigator.userAgent || navigator.vendor || window.opera;
    var isAndroid = /android/i.test(ua);
    var isFacebookOrInstagram = /FB_IAB|FBAN|Instagram/i.test(ua);
    var phoneMatch = originalUrl.match(/phone=([^&]+)/) || originalUrl.match(/wa\.me\/(\d+)/);
    var phone = phoneMatch ? phoneMatch[1] : null;

    if (!phone) {
        return originalUrl;
    }

    if (isFacebookOrInstagram) {
        return 'https://wa.me/' + phone;
    }

    if (isAndroid) {
        return 'whatsapp://send?phone=' + phone;
    }

    return originalUrl;
}

function handleStandaloneWhatsApp(e) {
    var url = this.getAttribute('data-whatsapp-url') || this.getAttribute('href');
    if (!url) return true;

    var finalUrl = getWhatsAppUrlForWebView(url);
    this.setAttribute('href', finalUrl);

    if (isWebViewEnv()) {
        e.preventDefault();
        e.stopImmediatePropagation();
        window.location.href = finalUrl;

        setTimeout(function() {
            var link = document.createElement('a');
            link.href = finalUrl;
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            setTimeout(function() {
                if (link.parentNode) {
                    document.body.removeChild(link);
                }
            }, 100);
        }, 10);

        return false;
    }

    return true;
}

function attachWhatsAppHandlers() {
    var whatsappButtons = document.querySelectorAll(
        'a[href*="api.whatsapp.com"]:not(.widget-connect__button-whatsapp), a[href*="wa.me"]:not(.widget-connect__button-whatsapp), a[data-whatsapp-url], a.whatsapp-link'
    );
    whatsappButtons.forEach(function(btn) {
        btn.removeEventListener('click', handleStandaloneWhatsApp, true);
        btn.addEventListener('click', handleStandaloneWhatsApp, true);
    });
}

document.addEventListener('DOMContentLoaded', attachWhatsAppHandlers);

if (typeof Livewire !== 'undefined') {
    Livewire.hook('morph.updated', function() {
        setTimeout(attachWhatsAppHandlers, 100);
    });
}

