@aware(['page'])
<section
    class="elementor-section elementor-top-section elementor-element elementor-element-8a27d02 elementor-section-boxed elementor-section-height-default"
    data-id="8a27d02" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
    <div class="elementor-container elementor-column-gap-default">
        <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-ad30fd9"
            data-id="ad30fd9" data-element_type="column">
            <div class="elementor-widget-wrap elementor-element-populated">
                <div class="elementor-element elementor-element-a06a370 elementor-widget elementor-widget-heading"
                    data-id="a06a370" data-element_type="widget" data-widget_type="heading.default">
                    <div class="elementor-widget-container">
                        <h2 class="elementor-heading-title elementor-size-default">{{$title}}</h2>
                    </div>
                </div>
                <section
                    class="elementor-section elementor-inner-section elementor-element elementor-element-edab5a8 elementor-section-boxed elementor-section-height-default"
                    data-id="edab5a8" data-element_type="section">
                    <div class="elementor-container elementor-column-gap-default">
                        @foreach($items as $item)
                        <div class="elementor-column elementor-col-25 elementor-inner-column elementor-element elementor-element-dc19911"
                            data-id="dc19911" data-element_type="column"
                            data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
                            <div class="elementor-widget-wrap elementor-element-populated">
                                <div class="elementor-element elementor-element-c7d23c7 elementor-widget elementor-widget-image"
                                    data-id="c7d23c7" data-element_type="widget" data-widget_type="image.default">
                                    <div class="elementor-widget-container">
                                        <img loading="lazy" decoding="async"
                                            src="{{asset('storage/'.$item['image'])}}"
                                            class="attachment-large size-large wp-image-193" alt="" />
                                    </div>
                                </div>
                                <div class="elementor-element elementor-element-ec9b944 elementor-widget elementor-widget-heading"
                                    data-id="ec9b944" data-element_type="widget" data-widget_type="heading.default">
                                    <div class="elementor-widget-container">
                                        <h2 class="elementor-heading-title elementor-size-default">{{$item['name']}}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </section>
            </div>
        </div>
    </div>
</section>
