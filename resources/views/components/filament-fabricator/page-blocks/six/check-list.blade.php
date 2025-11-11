@aware(['page'])
<section
    class="elementor-section elementor-top-section elementor-element elementor-element-4c0ac2d8 elementor-section-boxed elementor-section-height-default"
    data-id="4c0ac2d8" data-element_type="section">
    <div class="elementor-container elementor-column-gap-no">
        <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-6b6e9566"
            data-id="6b6e9566" data-element_type="column">
            <div class="elementor-widget-wrap elementor-element-populated">
                <div class="elementor-element elementor-element-cf187f0 elementor-widget elementor-widget-heading"
                    data-id="cf187f0" data-element_type="widget" data-widget_type="heading.default">
                    <div class="elementor-widget-container">
                        <h2 class="elementor-heading-title elementor-size-default">{{$title}}</h2>
                    </div>
                </div>
                <div class="elementor-element elementor-element-8e20db8 elementor-align-left elementor-icon-list--layout-traditional elementor-list-item-link-full_width elementor-widget elementor-widget-icon-list"
                    data-id="8e20db8" data-element_type="widget" data-widget_type="icon-list.default">
                    <div class="elementor-widget-container">
                        {!! $content !!}
                    </div>
                </div>
                <div class="elementor-element elementor-element-1dc7ff05 elementor-align-center elementor-widget elementor-widget-button"
                    data-id="1dc7ff05" data-element_type="widget" data-widget_type="button.default">
                    <div class="elementor-widget-container">
                        <div class="elementor-button-wrapper">
                            <a class="elementor-button elementor-button-link elementor-size-lg elementor-animation-push"
                                href="#order">
                                <span class="elementor-button-content-wrapper">
                                    <span class="elementor-button-text">অর্ডার করতে চাই</span>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
                @if($image)
                <div class="elementor-element elementor-element-9e86776 elementor-widget elementor-widget-image"
                    data-id="9e86776" data-element_type="widget" data-widget_type="image.default">
                    <div class="elementor-widget-container">
                        <img fetchpriority="high" decoding="async" width="1080" height="1080"
                            src="{{ Str::isUrl($image) ? $image : asset('storage/'.$image) }}"
                            class="attachment-large size-large wp-image-27434" alt=""
                            />
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
<section
    class="elementor-section elementor-top-section elementor-element elementor-element-6985a19 elementor-section-boxed elementor-section-height-default"
    data-id="6985a19" data-element_type="section">
    <div class="elementor-container elementor-column-gap-no">
        <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-91cf25b"
            data-id="91cf25b" data-element_type="column"
            data-settings="{&quot;background_background&quot;:&quot;classic&quot;}">
            <div class="elementor-widget-wrap elementor-element-populated">
                <div class="elementor-element elementor-element-f33d443 elementor-widget elementor-widget-heading"
                    data-id="f33d443" data-element_type="widget" data-widget_type="heading.default">
                    <div class="elementor-widget-container">
                        <h2 class="elementor-heading-title elementor-size-default">প্রয়োজনে ফোন করুন
                            {{setting('company')->phone ?? ''}}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
