@aware(['page'])
<section
    class="elementor-section elementor-top-section elementor-element elementor-element-c5a5ce4 elementor-section-boxed elementor-section-height-default"
    data-id="c5a5ce4" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
    <div class="elementor-container elementor-column-gap-default">
        <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-666e328"
            data-id="666e328" data-element_type="column">
            <div class="elementor-widget-wrap elementor-element-populated">
                <div class="elementor-element elementor-element-1632fd6 elementor-countdown--label-block elementor-invisible elementor-widget elementor-widget-countdown"
                    data-id="1632fd6" data-element_type="widget"
                    data-settings="{&quot;_animation&quot;:&quot;fadeInDown&quot;}"
                    data-widget_type="countdown.default">
                    <div class="elementor-widget-container">
                        <div class="elementor-countdown-wrapper" data-date="{{ strtotime($deadline) }}">
                            <div class="elementor-countdown-item"><span
                                    class="elementor-countdown-digits elementor-countdown-days"></span>
                                <span class="elementor-countdown-label">Days</span>
                            </div>
                            <div class="elementor-countdown-item"><span
                                    class="elementor-countdown-digits elementor-countdown-hours"></span>
                                <span class="elementor-countdown-label">Hours</span>
                            </div>
                            <div class="elementor-countdown-item"><span
                                    class="elementor-countdown-digits elementor-countdown-minutes"></span>
                                <span class="elementor-countdown-label">Minutes</span>
                            </div>
                            <div class="elementor-countdown-item"><span
                                    class="elementor-countdown-digits elementor-countdown-seconds"></span>
                                <span class="elementor-countdown-label">Seconds</span>
                            </div>
                        </div>
                    </div>
                </div>
                <section
                    class="elementor-section elementor-inner-section elementor-element elementor-element-bbde019 elementor-section-boxed elementor-section-height-default"
                    data-id="bbde019" data-element_type="section">
                    <div class="elementor-container elementor-column-gap-default">
                        <div class="elementor-column elementor-col-100 elementor-inner-column elementor-element elementor-element-284c050 elementor-invisible"
                            data-id="284c050" data-element_type="column"
                            data-settings="{&quot;animation&quot;:&quot;fadeInUp&quot;}">
                            <div class="elementor-widget-wrap elementor-element-populated">
                                <div class="elementor-element elementor-element-3b78435 elementor-widget elementor-widget-heading"
                                    data-id="3b78435" data-element_type="widget" data-widget_type="heading.default">
                                    <div class="elementor-widget-container">
                                        <h2 class="elementor-heading-title elementor-size-default">{{$title}}</h2>
                                    </div>
                                </div>
                                <div class="elementor-element elementor-element-9c51151 elementor-widget elementor-widget-heading"
                                    data-id="9c51151" data-element_type="widget" data-widget_type="heading.default">
                                    <div class="elementor-widget-container">
                                        <h2 class="elementor-heading-title elementor-size-default">{{$subtitle}}</h2>
                                    </div>
                                </div>
                                <div class="elementor-element elementor-element-f6824ca elementor-widget elementor-widget-heading"
                                    data-id="f6824ca" data-element_type="widget" data-widget_type="heading.default">
                                    <div class="elementor-widget-container">
                                        <h2 class="elementor-heading-title elementor-size-default">{{$note}}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</section>
