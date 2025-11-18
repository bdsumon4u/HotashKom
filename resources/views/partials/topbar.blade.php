<div class="site-header__topbar topbar text-nowrap">
    <div class="topbar__container container">
        <div class="topbar__row">
            @if ($show_option->topbar_phone ?? false)
            <div class="topbar__item topbar__item--link d-md-none">
                <img style="height: 35px;" class="img-responsive " src="https://www.himelshop.com/front_asset/call-now.gif" alt="Call 7colors" title="7colors">&nbsp;
                <a style="font-family: monospace;" class="topbar-link" href="tel:{{ $company->phone ?? '' }}">{{ $company->phone ?? '' }}</a>
            </div>
            @endif
            @foreach($menuItems as $item)
            @php
                $rawHref = $item->href;
                $isExternal = \Illuminate\Support\Str::startsWith($rawHref, ['http://', 'https://', 'mailto:', 'tel:', '#']);
                $href = $isExternal ? $rawHref : url($rawHref);
            @endphp
            <div class="topbar__item topbar__item--link d-none d-md-flex">
                <a class="topbar-link" href="{{ $href }}" @unless($isExternal) wire:navigate.hover @endunless>{!! $item->name !!}</a>
            </div>
            @endforeach
            <marquee class="d-flex align-items-center h-100 mx-2" behavior="" direction="">{!! $scroll_text ?? '' !!}</marquee>
            <div class="topbar__spring"></div>
            @if($show_option->track_order ?? false)
            <div class="topbar__item topbar__item--link">
                <a class="topbar-link" href="{{ url('/track-order') }}" wire:navigate.hover>Track Order</a>
            </div>
            @endif
        </div>
    </div>
</div>