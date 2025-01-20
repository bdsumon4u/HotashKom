@aware(['page'])
@isset($title)
<div class="elementor-element elementor-element-3c95a54 e-flex e-con-boxed e-con e-parent"
    data-id="3c95a54" data-element_type="container">
    <div class="e-con-inner">
        <div class="elementor-element elementor-element-0bb5f04 elementor-widget elementor-widget-heading"
            data-id="0bb5f04" data-element_type="widget" data-widget_type="heading.default">
            <div class="elementor-widget-container">
                <h2 class="elementor-heading-title elementor-size-default">{{$title}}</h2>
            </div>
        </div>
    </div>
</div>
@endisset
@if($leftTitle && $rightTitle)
<div class="elementor-element elementor-element-96adde6 e-flex e-con-boxed e-con e-parent" data-id="96adde6"
    data-element_type="container">
    <div class="e-con-inner">
        <div class="elementor-element elementor-element-fc20ff0 e-con-full e-flex e-con e-child" data-id="fc20ff0"
            data-element_type="container">
            <div class="elementor-element elementor-element-c379358 elementor-widget elementor-widget-heading"
                data-id="c379358" data-element_type="widget" data-widget_type="heading.default">
                <div class="elementor-widget-container">
                    <h2 class="elementor-heading-title elementor-size-default">{{ $leftTitle }}</h2>
                </div>
            </div>
        </div>
        <div class="elementor-element elementor-element-fd83858 e-con-full e-flex e-con e-child" data-id="fd83858"
            data-element_type="container">
            <div class="elementor-element elementor-element-f537b6e elementor-widget elementor-widget-heading"
                data-id="f537b6e" data-element_type="widget" data-widget_type="heading.default">
                <div class="elementor-widget-container">
                    <h2 class="elementor-heading-title elementor-size-default">{{ $rightTitle }}</h2>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<div class="elementor-element elementor-element-965b25e e-flex e-con-boxed e-con e-parent"
    data-id="965b25e" data-element_type="container">
    <div class="e-con-inner">
        <div class="elementor-element elementor-element-4650077 e-con-full e-flex e-con e-child"
            data-id="4650077" data-element_type="container">
            <div class="elementor-element elementor-element-e708d06 elementor-icon-list--layout-traditional elementor-list-item-link-full_width elementor-widget elementor-widget-icon-list"
                data-id="e708d06" data-element_type="widget" data-widget_type="icon-list.default">
                <div class="elementor-widget-container">
                    {!! $leftList !!}
                </div>
            </div>
        </div>
        <div class="elementor-element elementor-element-f65900a e-con-full e-flex e-con e-child"
            data-id="f65900a" data-element_type="container">
            <div class="elementor-element elementor-element-7913f0d elementor-icon-list--layout-traditional elementor-list-item-link-full_width elementor-widget elementor-widget-icon-list"
                data-id="7913f0d" data-element_type="widget" data-widget_type="icon-list.default">
                <div class="elementor-widget-container">
                    {!! $rightList !!}
                </div>
            </div>
        </div>
    </div>
</div>