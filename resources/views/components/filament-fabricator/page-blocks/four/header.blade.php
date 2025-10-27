@aware(['page'])
<section
    class="elementor-section elementor-top-section elementor-element elementor-element-d360dab elementor-section-boxed elementor-section-height-default"
    data-id="d360dab" data-element_type="section"
    data-settings="{&quot;background_background&quot;:&quot;classic&quot;,&quot;shape_divider_bottom&quot;:&quot;mountains&quot;}">
    <div class="elementor-shape elementor-shape-bottom" data-negative="false">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" preserveAspectRatio="none">
            <path class="elementor-shape-fill" opacity="0.33"
                d="M473,67.3c-203.9,88.3-263.1-34-320.3,0C66,119.1,0,59.7,0,59.7V0h1000v59.7 c0,0-62.1,26.1-94.9,29.3c-32.8,3.3-62.8-12.3-75.8-22.1C806,49.6,745.3,8.7,694.9,4.7S492.4,59,473,67.3z" />
            <path class="elementor-shape-fill" opacity="0.66"
                d="M734,67.3c-45.5,0-77.2-23.2-129.1-39.1c-28.6-8.7-150.3-10.1-254,39.1 s-91.7-34.4-149.2,0C115.7,118.3,0,39.8,0,39.8V0h1000v36.5c0,0-28.2-18.5-92.1-18.5C810.2,18.1,775.7,67.3,734,67.3z" />
            <path class="elementor-shape-fill"
                d="M766.1,28.9c-200-57.5-266,65.5-395.1,19.5C242,1.8,242,5.4,184.8,20.6C128,35.8,132.3,44.9,89.9,52.5C28.6,63.7,0,0,0,0 h1000c0,0-9.9,40.9-83.6,48.1S829.6,47,766.1,28.9z" />
        </svg>
    </div>
    <div class="elementor-container elementor-column-gap-default">
        <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-063a7ad"
            data-id="063a7ad" data-element_type="column">
            <div class="elementor-widget-wrap elementor-element-populated">
                <section
                    class="elementor-section elementor-inner-section elementor-element elementor-element-07d43da elementor-section-boxed elementor-section-height-default"
                    data-id="07d43da" data-element_type="section">
                    <div class="elementor-container elementor-column-gap-default">
                        <div class="elementor-column elementor-col-50 elementor-inner-column elementor-element elementor-element-9800953"
                            data-id="9800953" data-element_type="column">
                            <div class="elementor-widget-wrap elementor-element-populated">
                                <div class="elementor-element elementor-element-dd1e068 elementor-widget elementor-widget-heading"
                                    data-id="dd1e068" data-element_type="widget" data-widget_type="heading.default">
                                    <div class="elementor-widget-container">
                                        <h2 class="elementor-heading-title elementor-size-default">{{$title}}</h2>
                                    </div>
                                </div>
                                <div class="elementor-element elementor-element-d7af012 elementor-widget elementor-widget-heading"
                                    data-id="d7af012" data-element_type="widget" data-widget_type="heading.default">
                                    <div class="elementor-widget-container">
                                        {!! $content !!}
                                    </div>
                                </div>
                                <div class="elementor-element elementor-element-a95dc69 elementor-mobile-align-center elementor-widget elementor-widget-button"
                                    data-id="a95dc69" data-element_type="widget" data-widget_type="button.default">
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
                        <div class="elementor-column elementor-col-50 elementor-inner-column elementor-element elementor-element-1b406b0"
                            data-id="1b406b0" data-element_type="column">
                            <div class="elementor-widget-wrap elementor-element-populated">
                                <div class="elementor-element elementor-element-cca248f elementor-widget elementor-widget-image"
                                    data-id="cca248f" data-element_type="widget" data-widget_type="image.default">
                                    <div class="elementor-widget-container">
                                        <img decoding="async" width="418" height="418"
                                            src="{{ asset($thumbnail) }}"
                                            class="attachment-large size-large wp-image-209" alt="" />
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
