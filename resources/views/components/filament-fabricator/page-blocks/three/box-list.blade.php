@aware(['page'])
<section
    class="elementor-section elementor-top-section elementor-element elementor-element-1a38cca elementor-section-full_width elementor-section-height-default"
    data-id="1a38cca" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
    <div class="elementor-container elementor-column-gap-default">
        <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-c5ba803"
            data-id="c5ba803" data-element_type="column">
            <div class="elementor-widget-wrap elementor-element-populated">
                <div class="elementor-element elementor-element-f6af685 elementor-widget elementor-widget-heading"
                    data-id="f6af685" data-element_type="widget" data-widget_type="heading.default">
                    <div class="elementor-widget-container">
                        <h2 class="elementor-heading-title elementor-size-default">{{$title}}</h2>
                    </div>
                </div>
                <section
                    class="elementor-section elementor-inner-section elementor-element elementor-element-5a17fc3 elementor-section-boxed elementor-section-height-default"
                    data-id="5a17fc3" data-element_type="section">
                    <div class="elementor-container elementor-column-gap-default">
                        <div class="elementor-column elementor-col-100 elementor-inner-column elementor-element elementor-element-3e4875f"
                            data-id="3e4875f" data-element_type="column">
                            {!! $content !!}
                        </div>
                    </div>
                </section>
                <div class="elementor-element elementor-element-687b775 elementor-align-center elementor-invisible elementor-widget elementor-widget-button"
                    data-id="687b775" data-element_type="widget"
                    data-settings="{&quot;_animation&quot;:&quot;fadeInDown&quot;}" data-widget_type="button.default">
                    <div class="elementor-widget-container">
                        <div class="elementor-button-wrapper">
                            <a class="elementor-button elementor-button-link elementor-size-lg" href="#order-form">
                                <span class="elementor-button-content-wrapper">
                                    <span class="elementor-button-text">অর্ডার করুন</span>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
