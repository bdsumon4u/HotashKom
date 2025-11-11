@aware(['page'])
<section
    class="elementor-section elementor-top-section elementor-element elementor-element-3baae7be elementor-section-content-middle elementor-section-boxed elementor-section-height-default"
    data-id="3baae7be" data-element_type="section"
    style="padding: 0"
    data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
    <div class="elementor-background-overlay"></div>
    <div class="elementor-container elementor-column-gap-no">
        <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-4009a9ba"
            data-id="4009a9ba" data-element_type="column">
            <div class="elementor-widget-wrap elementor-element-populated">
                <div class="elementor-element elementor-element-706d30e elementor-arrows-position-inside elementor-widget elementor-widget-image-carousel"
                    data-id="706d30e" data-element_type="widget"
                    data-settings='{
  "slides_to_show": "{{$columns ?? 1}}",
  "navigation": "arrows",
  "autoplay_speed": 1000,
  "image_spacing_custom": {
    "unit": "px",
    "size": 10,
    "sizes": []
  },
  "autoplay": "yes",
  "pause_on_hover": "yes",
  "pause_on_interaction": "yes",
  "infinite": "yes",
  "speed": 500,
  "image_spacing_custom_tablet": {
    "unit": "px",
    "size": "",
    "sizes": []
  },
  "image_spacing_custom_mobile": {
    "unit": "px",
    "size": "",
    "sizes": []
  }
}'
                    data-widget_type="image-carousel.default">
                    <div class="elementor-widget-container">
                        <div class="elementor-image-carousel-wrapper swiper" role="region"
                            aria-roledescription="carousel" aria-label="Image Carousel" dir="{{$direction ?? 'ltr'}}">
                            <div class="elementor-image-carousel swiper-wrapper" aria-live="off">
                                @foreach($images ?: $page->product->additional_images->map->src as $image)
                                <div class="swiper-slide" role="group" aria-roledescription="slide" aria-label="{{$loop->iteration}} of {{count($images ?: $page->product->additional_images->map->src)}}">
                                    <figure class="swiper-slide-inner"><img style="width:100%" decoding="async" class="swiper-slide-image"
                                            src="{{ Str::isUrl($image) ? $image : asset('storage/'.$image) }}"
                                            alt="{{$loop->iteration}} of {{count($images ?: $page->product->additional_images->map->src)}}" /></figure>
                                </div>
                                @endforeach
                            </div>
                            <div class="elementor-swiper-button elementor-swiper-button-prev" role="button"
                                tabindex="0">
                                <svg aria-hidden="true" class="e-font-icon-svg e-eicon-chevron-left"
                                    viewBox="0 0 1000 1000" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M646 125C629 125 613 133 604 142L308 442C296 454 292 471 292 487 292 504 296 521 308 533L604 854C617 867 629 875 646 875 663 875 679 871 692 858 704 846 713 829 713 812 713 796 708 779 692 767L438 487 692 225C700 217 708 204 708 187 708 171 704 154 692 142 675 129 663 125 646 125Z">
                                    </path>
                                </svg>
                            </div>
                            <div class="elementor-swiper-button elementor-swiper-button-next" role="button"
                                tabindex="0">
                                <svg aria-hidden="true" class="e-font-icon-svg e-eicon-chevron-right"
                                    viewBox="0 0 1000 1000" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M696 533C708 521 713 504 713 487 713 471 708 454 696 446L400 146C388 133 375 125 354 125 338 125 325 129 313 142 300 154 292 171 292 187 292 204 296 221 308 233L563 492 304 771C292 783 288 800 288 817 288 833 296 850 308 863 321 871 338 875 354 875 371 875 388 867 400 854L696 533Z">
                                    </path>
                                </svg>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
