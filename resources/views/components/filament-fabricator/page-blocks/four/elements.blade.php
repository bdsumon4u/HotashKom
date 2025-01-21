@aware(['page'])
<section
    class="elementor-section elementor-top-section elementor-element elementor-element-44bb505 elementor-section-boxed elementor-section-height-default"
    data-id="44bb505" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
    <div class="elementor-container elementor-column-gap-default">
        <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-f858e67"
            data-id="f858e67" data-element_type="column">
            <div class="elementor-widget-wrap elementor-element-populated">
                <div class="elementor-element elementor-element-56ec4f8 elementor-widget elementor-widget-heading"
                    data-id="56ec4f8" data-element_type="widget" data-widget_type="heading.default">
                    <div class="elementor-widget-container">
                        <h2 class="elementor-heading-title elementor-size-default">{{$title}}</h2>
                    </div>
                </div>
                <section
                    class="elementor-section elementor-inner-section elementor-element elementor-element-57af82f elementor-section-boxed elementor-section-height-default"
                    data-id="57af82f" data-element_type="section">
                    <div class="elementor-container elementor-column-gap-default">
                        @foreach($items as $item)
                        <div class="elementor-column elementor-col-20 elementor-inner-column elementor-element elementor-element-69ae077"
                            data-id="69ae077" data-element_type="column">
                            <div class="elementor-widget-wrap elementor-element-populated">
                                <div class="elementor-element elementor-element-e196775 elementor-widget elementor-widget-image"
                                    data-id="e196775" data-element_type="widget" data-widget_type="image.default">
                                    <div class="elementor-widget-container">
                                        <img decoding="async"
                                            src="{{asset('storage/'.$item['image'])}}"
                                            class="attachment-large size-large wp-image-186" alt="" />
                                    </div>
                                </div>
                                <div class="elementor-element elementor-element-f8f8163 elementor-widget elementor-widget-heading"
                                    data-id="f8f8163" data-element_type="widget" data-widget_type="heading.default">
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
