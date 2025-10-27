@aware(['page'])
<section
    class="elementor-section elementor-top-section elementor-element elementor-element-99153fe elementor-section-content-middle elementor-section-boxed elementor-section-height-default"
    data-id="99153fe" data-element_type="section" data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
    <div class="elementor-background-overlay"></div>
    <div class="elementor-container elementor-column-gap-default">
        <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-96f2333"
            data-id="96f2333" data-element_type="column">
            <div class="elementor-widget-wrap elementor-element-populated">
                <div class="elementor-element elementor-element-c746378 elementor-widget elementor-widget-heading"
                    data-id="c746378" data-element_type="widget" data-widget_type="heading.default">
                    <div class="elementor-widget-container">
                        <h2 class="elementor-heading-title elementor-size-default">{{$title}}</h2>
                    </div>
                </div>
                <section
                    class="elementor-section elementor-inner-section elementor-element elementor-element-ef20178 elementor-section-content-middle elementor-section-boxed elementor-section-height-default"
                    data-id="ef20178" data-element_type="section">
                    <div class="elementor-container elementor-column-gap-default">
                        <div class="elementor-column elementor-col-50 elementor-inner-column elementor-element elementor-element-3c71c4c"
                            data-id="3c71c4c" data-element_type="column">
                            <div class="elementor-widget-wrap elementor-element-populated">
                                <div class="elementor-element elementor-element-e5992bb elementor-icon-list--layout-traditional elementor-list-item-link-full_width elementor-widget elementor-widget-icon-list"
                                    data-id="e5992bb" data-element_type="widget" data-widget_type="icon-list.default">
                                    <div class="elementor-widget-container">
                                        {!! $content !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="elementor-column elementor-col-50 elementor-inner-column elementor-element elementor-element-0f69eb6"
                            data-id="0f69eb6" data-element_type="column">
                            <div class="elementor-widget-wrap elementor-element-populated">
                                <div class="elementor-element elementor-element-cb24452 elementor-widget elementor-widget-image"
                                    data-id="cb24452" data-element_type="widget" data-widget_type="image.default">
                                    <div class="elementor-widget-container">
                                        @php($image ??= $page->product->base_image->src)
                                        <img loading="lazy" decoding="async" width="350" height="350"
                                            src="{{Str::isUrl($image) ? $image : asset('storage/'.$image)}}"
                                            class="attachment-large size-large wp-image-191" alt="" />
                                    </div>
                                </div>
                                <div class="elementor-element elementor-element-086665d elementor-align-center elementor-widget elementor-widget-button"
                                    data-id="086665d" data-element_type="widget" data-widget_type="button.default">
                                    <div class="elementor-widget-container">
                                        <div class="elementor-button-wrapper">
                                            <a class="elementor-button elementor-button-link elementor-size-sm"
                                                href="#order">
                                                <span class="elementor-button-content-wrapper">
                                                    <span class="elementor-button-icon">
                                                        <svg aria-hidden="true"
                                                            class="e-font-icon-svg e-far-arrow-alt-circle-right"
                                                            viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M504 256C504 119 393 8 256 8S8 119 8 256s111 248 248 248 248-111 248-248zm-448 0c0-110.5 89.5-200 200-200s200 89.5 200 200-89.5 200-200 200S56 366.5 56 256zm72 20v-40c0-6.6 5.4-12 12-12h116v-67c0-10.7 12.9-16 20.5-8.5l99 99c4.7 4.7 4.7 12.3 0 17l-99 99c-7.6 7.6-20.5 2.2-20.5-8.5v-67H140c-6.6 0-12-5.4-12-12z">
                                                            </path>
                                                        </svg> </span>
                                                    <span class="elementor-button-text">{{ __('button.click_to_order') }}</span>
                                                </span>
                                            </a>
                                        </div>
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
