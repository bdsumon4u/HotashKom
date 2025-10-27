@aware(['page'])
<section
    class="elementor-section elementor-top-section elementor-element elementor-element-6985a19 elementor-section-boxed elementor-section-height-default"
    data-id="6985a19" data-element_type="section">
    <div class="elementor-container elementor-column-gap-no">
        <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-91cf25b"
            data-id="91cf25b" data-element_type="column"
            data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
            <div class="elementor-widget-wrap elementor-element-populated">
                <div class="elementor-element elementor-element-7e9d430 elementor-widget elementor-widget-heading"
                    data-id="7e9d430" data-element_type="widget" data-widget_type="heading.default">
                    <div class="elementor-widget-container">
                        <h2 class="elementor-heading-title elementor-size-default">{{ $instruction }}</h2>
                    </div>
                </div>
                <div class="elementor-element elementor-element-f33d443 elementor-widget elementor-widget-heading"
                    data-id="f33d443" data-element_type="widget" data-widget_type="heading.default">
                    <div class="elementor-widget-container">
                        <h2 class="elementor-heading-title elementor-size-default">{{ __('fabricator.call_us') }}</h2>
                    </div>
                </div>
                <div class="elementor-element elementor-element-197cb12 elementor-align-center elementor-widget elementor-widget-button"
                    data-id="197cb12" data-element_type="widget" data-widget_type="button.default">
                    <div class="elementor-widget-container">
                        <div class="elementor-button-wrapper">
                            <a class="elementor-button elementor-button-link elementor-size-lg elementor-animation-push"
                                href="tel:{{$contactNumber}}">
                                <span class="elementor-button-content-wrapper">
                                    <span class="elementor-button-icon">
                                        <svg aria-hidden="true" class="e-font-icon-svg e-fas-phone-alt"
                                            viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M497.39 361.8l-112-48a24 24 0 0 0-28 6.9l-49.6 60.6A370.66 370.66 0 0 1 130.6 204.11l60.6-49.6a23.94 23.94 0 0 0 6.9-28l-48-112A24.16 24.16 0 0 0 122.6.61l-104 24A24 24 0 0 0 0 48c0 256.5 207.9 464 464 464a24 24 0 0 0 23.4-18.6l24-104a24.29 24.29 0 0 0-14.01-27.6z">
                                            </path>
                                        </svg> </span>
                                    <span class="elementor-button-text">{{ $contactNumber }}</span>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
