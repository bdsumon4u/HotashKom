@aware(['page'])
<section
    class="elementor-section elementor-top-section elementor-element elementor-element-d207a7a elementor-section-content-middle elementor-section-boxed elementor-section-height-default"
    data-id="d207a7a" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
    <div class="elementor-container elementor-column-gap-no">
        <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-7147666"
            data-id="7147666" data-element_type="column">
            <div class="elementor-widget-wrap elementor-element-populated">
                <div class="elementor-element elementor-element-7c15ffa elementor-widget elementor-widget-heading"
                    data-id="7c15ffa" data-element_type="widget" data-widget_type="heading.default">
                    <div class="elementor-widget-container">
                        <h2 class="elementor-heading-title elementor-size-default">{{ $title }}</h2>
                    </div>
                </div>
                <div class="elementor-element elementor-element-ccbd414 elementor-arrows-position-inside elementor-pagination-position-outside elementor-widget elementor-widget-image-carousel"
                    data-id="ccbd414" data-element_type="widget"
                    data-settings="{&quot;navigation&quot;:&quot;both&quot;,&quot;autoplay&quot;:&quot;yes&quot;,&quot;pause_on_hover&quot;:&quot;yes&quot;,&quot;pause_on_interaction&quot;:&quot;yes&quot;,&quot;autoplay_speed&quot;:5000,&quot;infinite&quot;:&quot;yes&quot;,&quot;speed&quot;:500}"
                    data-widget_type="image-carousel.default">
                    <div class="elementor-widget-container">
                        <div class="elementor-image-carousel-wrapper swiper" dir="ltr">
                            <div class="elementor-image-carousel swiper-wrapper" aria-live="off">
                                @php
                                    // If $images is empty, prepend base image, add additional images, append variant base images, keep unique
                                    // Otherwise, use $images directly
                                    if (empty($images)) {
                                        $baseImage = $page->product->base_image ? collect([$page->product->base_image]) : collect();
                                        $additionalImages = $page->product->additional_images ?? collect();
                                        $variantImages = $page->product->variations->pluck('base_image')->filter();
                                        $allImages = $baseImage->merge($additionalImages)->merge($variantImages)->unique('id');
                                        $imageList = $allImages->map(fn($img) => $img->src ?? $img)->values();
                                    } else {
                                        $imageList = is_array($images) ? $images : collect($images);
                                    }
                                @endphp
                                @foreach($imageList as $image)
                                <div class="swiper-slide" role="group" aria-roledescription="slide">
                                    <figure class="swiper-slide-inner"><img decoding="async"
                                            class="swiper-slide-image"
                                            src="{{ Str::isUrl($image) ? $image : asset('storage/'.$image) }}"
                                            alt="" /></figure>
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

                            <div class="swiper-pagination"></div>
                        </div>
                    </div>
                </div>
                <div class="elementor-element elementor-element-61b505b elementor-element-1a4b772 elementor-align-left elementor-icon-list--layout-traditional elementor-list-item-link-full_width elementor-widget elementor-widget-icon-list"
                    data-id="1a4b772" data-element_type="widget" data-widget_type="icon-list.default">
                    <div class="elementor-widget-container">
                        {!! $description !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
