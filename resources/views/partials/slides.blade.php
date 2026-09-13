@php
    $slideItems = slides();

    $optimizedSlideSrc = static function ($src) {
        if (empty($src)) {
            return null;
        }

        $webpSrc = preg_replace('/\.(png|jpe?g)$/i', '.webp', $src);

        if ($webpSrc && file_exists(public_path(ltrim($webpSrc, '/')))) {
            return $webpSrc;
        }

        return $src;
    };

    $firstSlide = $slideItems->first();

    $firstDesktopSrc = $firstSlide
        ? $optimizedSlideSrc($firstSlide->desktop_src)
        : null;

    $firstMobileSrc = $firstSlide
        ? $optimizedSlideSrc($firstSlide->mobile_src)
        : null;

    $lcpDesktopImageUrl = $firstDesktopSrc
        ? cdn(asset($firstDesktopSrc), 840, 395)
        : null;

    $lcpMobileImageUrl = $firstMobileSrc
        ? cdn(asset($firstMobileSrc), 768, 361)
        : null;
@endphp

@push('head')
    @if($lcpMobileImageUrl)
        <link
            rel="preload"
            as="image"
            href="{{ $lcpMobileImageUrl }}?v=20260803-768q90"
            media="(max-width: 1023px)"
            fetchpriority="high">
    @endif

    @if($lcpDesktopImageUrl)
        <link
            rel="preload"
            as="image"
            href="{{ $lcpDesktopImageUrl }}"
            media="(min-width: 1024px)"
            fetchpriority="high">
    @endif
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var slideshowContainer = document.querySelector('.block-slideshow .owl-carousel');
        if (slideshowContainer) {
            var observer = new MutationObserver(function () {
                slideshowContainer.querySelectorAll('.owl-prev[role="presentation"], .owl-next[role="presentation"]').forEach(function (el) {
                    el.removeAttribute('role');
                });
                var prev = slideshowContainer.querySelector('.owl-prev');
                var next = slideshowContainer.querySelector('.owl-next');
                if (prev && !prev.getAttribute('aria-label')) {
                    prev.setAttribute('aria-label', 'Previous slide');
                }
                if (next && !next.getAttribute('aria-label')) {
                    next.setAttribute('aria-label', 'Next slide');
                }
                slideshowContainer.querySelectorAll('.owl-dot').forEach(function (dot, index) {
                    if (!dot.getAttribute('aria-label')) {
                        dot.setAttribute('aria-label', 'Go to slide ' + (index + 1));
                    }
                });
            });
            observer.observe(slideshowContainer, {childList: true, subtree: true});
        }
    });
</script>
@endpush

@push('styles')
<style>
    /* LCP: show the first slide before Owl Carousel initializes */
    .block-slideshow__body .owl-carousel:not(.owl-loaded) {
        display: block;
    }

    .block-slideshow__body .owl-carousel:not(.owl-loaded) > .block-slideshow__slide {
        display: none;
    }

    .block-slideshow__body .owl-carousel:not(.owl-loaded) > .block-slideshow__slide:first-child {
        display: block;
    }
    @if(!(setting('show_option')->category_dropdown ?? false))
    .block-slideshow--layout--with-departments .block-slideshow__body {
        margin-left: 0;
    }
    @endif

    .block-slideshow {
        margin-top: 15px;
        margin-bottom: 35px !important;
    }

    .block-slideshow__body {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.10);
        position: relative;
    }

    .block-slideshow__slide {
        border-radius: 8px;
        overflow: hidden;
    }

    .block-slideshow__slide-image--mobile {
        display: none;
    }

    @media (max-width: 1023px) {
        .block-slideshow__slide-image--desktop {
            display: none;
        }

        .block-slideshow__slide-image--mobile {
            display: block;
        }
    }

    .block-slideshow__body .owl-carousel {
        position: relative;
    }

    .block-slideshow__body .owl-carousel .owl-nav {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        pointer-events: none !important;
        z-index: 10 !important;
        display: block !important;
    }

    .block-slideshow__body .owl-carousel .owl-nav button.owl-prev,
    .block-slideshow__body .owl-carousel .owl-nav button.owl-next {
        position: absolute !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        width: 40px !important;
        height: 40px !important;
        border-radius: 50% !important;
        color: #ffffff !important;
        background: rgba(15, 23, 42, 0.55) !important;
        backdrop-filter: blur(4px);
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        border: 1.5px solid rgba(255, 255, 255, 0.3) !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;
        cursor: pointer !important;
        pointer-events: auto !important;
        margin: 0 !important;
        padding: 0 !important;
        transition: all 0.2s ease !important;
        z-index: 11 !important;
    }

    .block-slideshow__body .owl-carousel .owl-nav button.owl-prev {
        left: 14px !important;
        right: auto !important;
    }

    .block-slideshow__body .owl-carousel .owl-nav button.owl-next {
        right: 14px !important;
        left: auto !important;
    }

    .block-slideshow__body .owl-carousel .owl-nav button:hover {
        background: var(--brand) !important;
        border-color: var(--brand) !important;
        transform: translateY(-50%) scale(1.08) !important;
    }

    .block-slideshow__body .owl-carousel .owl-nav button:focus {
        outline: none;
    }

    .block-slideshow .owl-carousel .owl-dots {
        position: absolute;
        bottom: 12px;
        left: 0;
        right: 0;
        display: flex;
        justify-content: center;
        gap: 6px;
    }

    .block-slideshow .owl-carousel .owl-dot {
        width: 8px !important;
        height: 8px !important;
        border-radius: 9999px !important;
        background: rgba(255, 255, 255, 0.5) !important;
        transition: all 0.25s ease;
    }

    .block-slideshow .owl-carousel .owl-dot.active {
        width: 24px !important;
        background: #ffffff !important;
    }

    @media (max-width: 749px) {
        .block-slideshow {
            margin-top: 10px;
            margin-bottom: 25px;
        }
        #slideshow-container {
            padding-left: 8px;
            padding-right: 8px;
        }
        .block-slideshow__body {
            border-radius: 12px;
        }
    }

    .block-slideshow__slide-image img {
        display: block;
    }
    .block-slideshow__slide-image--desktop img,
    .block-slideshow__slide-image--mobile img {
        min-width: 100%;
        min-height: 100%;
    }
    @media (max-width: 1023px) {
        .block-slideshow__body, .block-slideshow__slide {
            height: 190px !important;
        }
    }
</style>
@endpush
<div class="block block-slideshow block-slideshow--layout--with-departments">
    <div id="slideshow-container" class="container">
        <div class="row">
            <div class="col-12 @if(setting('show_option')->category_dropdown ?? false) col-lg-9 offset-lg-3 @endif">
                <div class="block-slideshow__body">
                    <div class="owl-carousel">
                         @foreach($slideItems as $slide)
    @php
        $desktopSrc = $optimizedSlideSrc($slide->desktop_src);
        $mobileSrc = $optimizedSlideSrc($slide->mobile_src);

        $desktopImageUrl = cdn(asset($desktopSrc), 840, 395);
        $mobileImageUrl = cdn(asset($mobileSrc), 768, 361);

        $isFirstSlide = $loop->first;
        $objectFit = $slide->object_fit ?? 'cover';
        $slideAlt = strip_tags($slide->title ?: (($company->name ?? config('app.name')) . ' promotional banner'));
    @endphp

    <a class="block-slideshow__slide" href="{{ $slide->btn_href ?? '#' }}">
        <div
            class="block-slideshow__slide-image"
            style="position: relative; overflow: hidden;">

            <picture style="display: block; width: 100%; height: 100%;">
                <source
                    media="(max-width: 1023px)"
                    srcset="{{ $mobileImageUrl }}?v=20260803-768q90">

                <img
                    src="{{ $desktopImageUrl }}"
                    alt="{{ $slideAlt }}"
                    width="840"
                    height="395"
                    decoding="async"
                    @if($isFirstSlide)
                        fetchpriority="high"
                        loading="eager"
                    @else
                        fetchpriority="low"
                        loading="lazy"
                    @endif
                    style="width: 100%; height: 100%; object-fit: {{ $objectFit }}; position: absolute; top: 0; left: 0;">
            </picture>
        </div>

        <div class="block-slideshow__slide-content">
            <div class="block-slideshow__slide-title">{!! $slide->title !!}</div>
            <div class="block-slideshow__slide-text">{!! $slide->text !!}</div>

            @if($slide->btn_href && $slide->btn_name)
                <div class="block-slideshow__slide-button">
                    <span class="btn btn-primary btn-lg">
                        {{ $slide->btn_name }}
                    </span>
                </div>
            @endif
        </div>
    </a>
@endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
