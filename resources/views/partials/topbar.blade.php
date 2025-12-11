<div class="site-header__topbar topbar text-nowrap">
    <div class="container topbar__container">
        <div class="topbar__row">
            @if ($show_option->topbar_phone ?? false)
            <div class="topbar__item topbar__item--link d-md-none">
                {{-- Lazy load call-now.gif - it's 279KB and not critical for initial render --}}
                <img style="height: 35px; width: auto; display: block;" class="img-responsive" src="{{ asset('call-now.gif') }}" width="auto" height="35" loading="lazy" decoding="async">&nbsp;
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
            <marquee class="mx-2 d-flex align-items-center h-100" behavior="" direction="">{!! $scroll_text ?? '' !!}</marquee>
            <div class="topbar__spring"></div>
            @if($show_option->track_order ?? false)
            <div class="topbar__item topbar__item--link">
                <a class="topbar-link" href="{{ url('/track-order') }}" wire:navigate.hover>Track Order</a>
            </div>
            @endif
        </div>
    </div>
</div>
