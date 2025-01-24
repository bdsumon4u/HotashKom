@aware(['page'])
<section
    class="elementor-section elementor-top-section elementor-element elementor-element-0397245 elementor-section-boxed elementor-section-height-default"
    data-id="0397245" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
    <div class="elementor-container elementor-column-gap-default">
        <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-7f14e11"
            data-id="7f14e11" data-element_type="column">
            <div class="elementor-widget-wrap elementor-element-populated">
                <div class="elementor-element elementor-element-782c295 elementor-widget elementor-widget-heading"
                    data-id="782c295" data-element_type="widget" data-widget_type="heading.default">
                    <div class="elementor-widget-container">
                        <h2 class="elementor-heading-title elementor-size-default">{{$title}}</h2>
                    </div>
                </div>
                <div class="elementor-element elementor-element-2d90bf6 elementor-pagination-position-outside elementor-widget elementor-widget-image-carousel"
                    data-id="2d90bf6" data-element_type="widget"
                    data-settings="{&quot;slides_to_show&quot;:&quot;1&quot;,&quot;navigation&quot;:&quot;dots&quot;,&quot;lazyload&quot;:&quot;yes&quot;,&quot;autoplay_speed&quot;:6000,&quot;speed&quot;:600,&quot;autoplay&quot;:&quot;yes&quot;,&quot;pause_on_hover&quot;:&quot;yes&quot;,&quot;pause_on_interaction&quot;:&quot;yes&quot;,&quot;infinite&quot;:&quot;yes&quot;,&quot;effect&quot;:&quot;slide&quot;}"
                    data-widget_type="image-carousel.default">
                    <div class="elementor-widget-container">
                        <div class="elementor-image-carousel-wrapper swiper" dir="ltr">
                            <div class="elementor-image-carousel swiper-wrapper" aria-live="off">
                                @foreach($images ?: $page->product->additional_images->map->src as $image)
                                <div class="swiper-slide" role="group" aria-roledescription="slide">
                                    <figure class="swiper-slide-inner"><img class="swiper-slide-image swiper-lazy"
                                            data-src="{{Str::isUrl($image) ? $image : asset('storage/'.$image)}}" />
                                        <div class="swiper-lazy-preloader"></div>
                                    </figure>
                                </div>
                                @endforeach
                            </div>

                            <div class="swiper-pagination"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
