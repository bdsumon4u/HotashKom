@aware(['page'])
<section
    class="elementor-section elementor-top-section elementor-element elementor-element-a525304 elementor-section-boxed elementor-section-height-default"
    data-id="a525304" data-element_type="section"
    data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
    <div class="elementor-container elementor-column-gap-default">
        <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-ff2e9d0"
            data-id="ff2e9d0" data-element_type="column">
            <div class="elementor-widget-wrap elementor-element-populated">
                <div class="elementor-element elementor-element-f649bb5 elementor-widget elementor-widget-heading"
                    data-id="f649bb5" data-element_type="widget" data-widget_type="heading.default">
                    <div class="elementor-widget-container">
                        <h2 class="elementor-heading-title elementor-size-default">{{$title}}</h2>
                    </div>
                </div>
                <div class="elementor-element elementor-element-7f6458e elementor-align-left elementor-mobile-align-left elementor-icon-list--layout-traditional elementor-list-item-link-full_width elementor-invisible elementor-widget elementor-widget-icon-list"
                    data-id="7f6458e" data-element_type="widget"
                    data-settings="{&quot;_animation&quot;:&quot;fadeInLeft&quot;}"
                    data-widget_type="icon-list.default">
                    <div class="elementor-widget-container">
                        {!! $content !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>