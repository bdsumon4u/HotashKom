@aware(['page'])
<section
    class="elementor-section elementor-top-section elementor-element elementor-element-fe46170 elementor-section-boxed elementor-section-height-default"
    data-id="fe46170" data-element_type="section"
    data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
    <div class="elementor-container elementor-column-gap-no">
        <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-b7e43c7"
            data-id="b7e43c7" data-element_type="column">
            <div class="elementor-widget-wrap elementor-element-populated">
                <div class="elementor-element elementor-element-c8e70d8 elementor-arrows-position-inside elementor-pagination-position-outside elementor-widget elementor-widget-image-carousel"
                    data-id="c8e70d8" data-element_type="widget"
                    data-settings="{&quot;slides_to_show&quot;:&quot;3&quot;,&quot;slides_to_show_mobile&quot;:&quot;2&quot;,&quot;navigation&quot;:&quot;both&quot;,&quot;autoplay&quot;:&quot;yes&quot;,&quot;pause_on_hover&quot;:&quot;yes&quot;,&quot;pause_on_interaction&quot;:&quot;yes&quot;,&quot;autoplay_speed&quot;:5000,&quot;infinite&quot;:&quot;yes&quot;,&quot;speed&quot;:500,&quot;image_spacing_custom&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:20,&quot;sizes&quot;:[]},&quot;image_spacing_custom_tablet&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]},&quot;image_spacing_custom_mobile&quot;:{&quot;unit&quot;:&quot;px&quot;,&quot;size&quot;:&quot;&quot;,&quot;sizes&quot;:[]}}"
                    data-widget_type="image-carousel.default">
                    <div class="elementor-widget-container">
                        <div class="elementor-image-carousel-wrapper swiper image-carousel-{{ $page->id ?? 'default' }}" dir="ltr" role="region" aria-roledescription="carousel" aria-label="Image Carousel">
                            <div class="elementor-image-carousel swiper-wrapper" aria-live="off">
                                @foreach($images ?: $page->product->additional_images->map->src as $image)
                                <div class="swiper-slide" role="group" aria-roledescription="slide" aria-label="{{$loop->iteration}} of {{count($images ?: $page->product->additional_images->map->src)}}">
                                    <figure class="swiper-slide-inner"><img decoding="async"
                                            class="swiper-slide-image"
                                            src="{{ Str::isUrl($image) ? $image : asset('storage/'.$image) }}"
                                            alt="{{$loop->iteration}} of {{count($images ?: $page->product->additional_images->map->src)}}" /></figure>
                                </div>
                                @endforeach
                            </div>
                            <div class="elementor-swiper-button elementor-swiper-button-prev swiper-button-prev"
                                role="button" tabindex="0" aria-label="Previous slide">
                                <svg aria-hidden="true" class="e-font-icon-svg e-eicon-chevron-left"
                                    viewBox="0 0 1000 1000" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M646 125C629 125 613 133 604 142L308 442C296 454 292 471 292 487 292 504 296 521 308 533L604 854C617 867 629 875 646 875 663 875 679 871 692 858 704 846 713 829 713 812 713 796 708 779 692 767L438 487 692 225C700 217 708 204 708 187 708 171 704 154 692 142 675 129 663 125 646 125Z">
                                    </path>
                                </svg>
                            </div>
                            <div class="elementor-swiper-button elementor-swiper-button-next swiper-button-next"
                                role="button" tabindex="0" aria-label="Next slide">
                                <svg aria-hidden="true" class="e-font-icon-svg e-eicon-chevron-right"
                                    viewBox="0 0 1000 1000" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M696 533C708 521 713 504 713 487 713 471 708 454 696 446L400 146C388 133 375 125 354 125 338 125 325 129 313 142 300 154 292 171 292 187 292 204 296 221 308 233L563 492 304 771C292 783 288 800 288 817 288 833 296 850 308 863 321 871 338 875 354 875 371 875 388 867 400 854L696 533Z">
                                    </path>
                                </svg>
                            </div>

                            <div class="swiper-pagination"></div>
                        </div>
                    </div>
                    <script>
                        (function() {
                            const carouselId = 'image-carousel-{{ $page->id ?? 'default' }}';
                            function initCarousel() {
                                const carouselWrapper = document.querySelector('.' + carouselId);
                                if (carouselWrapper && typeof Swiper !== 'undefined') {
                                    new Swiper(carouselWrapper, {
                                        slidesPerView: 3,
                                        spaceBetween: 20,
                                        loop: true,
                                        autoplay: {
                                            delay: 5000,
                                            pauseOnMouseEnter: true,
                                            disableOnInteraction: false,
                                        },
                                        speed: 500,
                                        navigation: {
                                            nextEl: carouselWrapper.querySelector('.swiper-button-next'),
                                            prevEl: carouselWrapper.querySelector('.swiper-button-prev'),
                                        },
                                        pagination: {
                                            el: carouselWrapper.querySelector('.swiper-pagination'),
                                            clickable: true,
                                        },
                                        breakpoints: {
                                            0: {
                                                slidesPerView: 2,
                                                spaceBetween: 10,
                                            },
                                            768: {
                                                slidesPerView: 3,
                                                spaceBetween: 20,
                                            },
                                        },
                                    });
                                } else if (carouselWrapper && typeof Swiper === 'undefined') {
                                    // Retry after a short delay if Swiper is not loaded yet
                                    setTimeout(initCarousel, 100);
                                }
                            }
                            if (document.readyState === 'loading') {
                                document.addEventListener('DOMContentLoaded', initCarousel);
                            } else {
                                initCarousel();
                            }
                        })();
                    </script>
                </div>
            </div>
        </div>
    </div>
</section>