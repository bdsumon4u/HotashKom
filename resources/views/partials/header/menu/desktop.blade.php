<div class="nav-panel__nav-links nav-links">
    <ul class="nav-links__list">
        @foreach($menuItems as $item)
        @php
            $rawHref = $item->href;
            $isExternal = \Illuminate\Support\Str::startsWith($rawHref, ['http://', 'https://', 'mailto:', 'tel:', '#']);
            $href = $isExternal ? $rawHref : url($rawHref);
        @endphp
        <li class="nav-links__item">
            <a href="{{ $href }}" @unless($isExternal) wire:navigate.hover @endunless>
                <span>{{ $item->name }}</span>
            </a>
        </li>
        @endforeach
    </ul>
</div>