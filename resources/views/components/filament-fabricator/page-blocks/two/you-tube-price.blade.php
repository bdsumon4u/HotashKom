@aware(['page'])
<section
    class="elementor-section elementor-top-section elementor-element elementor-element-4c0ac2d8 elementor-section-boxed elementor-section-height-default"
    data-id="4c0ac2d8" data-element_type="section">
    <div class="elementor-container elementor-column-gap-no">
        <div class="elementor-column elementor-col-100 elementor-top-column elementor-element elementor-element-6b6e9566"
            data-id="6b6e9566" data-element_type="column">
            <div class="elementor-widget-wrap elementor-element-populated">
                <div class="elementor-element elementor-element-33d3cc3b elementor-widget elementor-widget-video"
                    data-id="33d3cc3b" data-element_type="widget"
                    data-settings="{&quot;youtube_url&quot;:&quot;{{$youtubeLink}}&quot;,&quot;autoplay&quot;:&quot;yes&quot;,&quot;play_on_mobile&quot;:&quot;yes&quot;,&quot;loop&quot;:&quot;yes&quot;,&quot;video_type&quot;:&quot;youtube&quot;,&quot;controls&quot;:&quot;yes&quot;}"
                    data-widget_type="video.default">
                    <div class="elementor-widget-container">
                        <div class="elementor-wrapper elementor-open-inline">
                            <div class="elementor-video"></div>
                        </div>
                    </div>
                </div>
                <div class="elementor-element elementor-element-cf187f0 elementor-widget elementor-widget-heading"
                    data-id="cf187f0" data-element_type="widget" data-widget_type="heading.default">
                    <div class="elementor-widget-container">
                        <h2 class="elementor-heading-title elementor-size-default">{{$title}}</h2>
                    </div>
                </div>
                <div class="elementor-element elementor-element-2a3bc9ca elementor-widget elementor-widget-text-editor"
                    data-id="2a3bc9ca" data-element_type="widget" data-widget_type="text-editor.default">
                    <div class="elementor-widget-container">
                        {!! $description !!}
                    </div>
                </div>
                <div class="elementor-element elementor-element-40e524f4 elementor-widget elementor-widget-price-list"
                    data-id="40e524f4" data-element_type="widget" data-widget_type="price-list.default">
                    <div class="elementor-widget-container">

                        <ul class="elementor-price-list">

                            <li><a class="elementor-price-list-item" href="#">
                                    <div class="elementor-price-list-text">
                                        <div class="elementor-price-list-header">
                                            <span class="elementor-price-list-title">{{ $priceText }}</span>
                                            <span class="elementor-price-list-separator"></span>
                                            <span class="elementor-price-list-price">৳{{ $priceAmount }}</span>
                                        </div>
                                        <p class="elementor-price-list-description">{{ $priceSubtext }}</p>
                                    </div>
                                </a></li>
                        </ul>

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
                <div class="elementor-element elementor-element-9657489 elementor-widget elementor-widget-image"
                    data-id="9657489" data-element_type="widget" data-widget_type="image.default">
                    <div class="elementor-widget-container">
                        <img decoding="async" width="1" height="1"
                            src="https://demo.orioit.com/wp-content/uploads/2024/11/WhatsApp-Image-2024-02-24-at-10.28.07-PM-1024x1024-1.jpeg"
                            class="attachment-large size-large wp-image-131" alt="" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
