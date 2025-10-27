@aware(['page'])
<section
    class="elementor-section elementor-top-section elementor-element elementor-element-3baae7be elementor-section-content-middle elementor-section-boxed elementor-section-height-default"
    data-id="3baae7be" data-element_type="section"
    data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
    <div class="elementor-background-overlay"></div>
    <div class="elementor-container elementor-column-gap-no">
        <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-4009a9ba"
            data-id="4009a9ba" data-element_type="column">
            <div class="elementor-widget-wrap elementor-element-populated">
                <div class="elementor-element elementor-element-e938b01 elementor-widget elementor-widget-image"
                    data-id="e938b01" data-element_type="widget" data-widget_type="image.default">
                    <div class="elementor-widget-container">
                        <img decoding="async"
                            src="{{ asset(setting('logo')->desktop) }}"
                            class="attachment-full size-full wp-image-128" alt="" />
                    </div>
                </div>
                <div class="elementor-element elementor-element-34de74d elementor-widget elementor-widget-heading"
                    data-id="34de74d" data-element_type="widget" data-widget_type="heading.default">
                    <div class="elementor-widget-container">
                        <h2 class="elementor-heading-title elementor-size-default">{{$heading}}</h2>
                    </div>
                </div>
                <div class="elementor-element elementor-element-6ca1165 elementor-widget elementor-widget-text-editor"
                    data-id="6ca1165" data-element_type="widget"
                    data-widget_type="text-editor.default">
                    <div class="elementor-widget-container">
                        <p>{{$subheading}}</p>
                    </div>
                </div>
                <div class="elementor-element elementor-element-343882d7 elementor-align-center elementor-widget elementor-widget-button"
                    data-id="343882d7" data-element_type="widget" data-widget_type="button.default">
                    <div class="elementor-widget-container">
                        <div class="elementor-button-wrapper">
                            <a class="elementor-button elementor-button-link elementor-size-lg elementor-animation-push"
                                href="#order">
                                <span class="elementor-button-content-wrapper">
                                    <span class="elementor-button-text">{{ __('button.want_order') }}</span>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
