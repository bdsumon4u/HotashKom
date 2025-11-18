@foreach($menuItems as $item)
@php
    $rawHref = $item->href;
    $isExternal = \Illuminate\Support\Str::startsWith($rawHref, ['http://', 'https://', 'mailto:', 'tel:', '#']);
    $href = $isExternal ? $rawHref : url($rawHref);
@endphp
<li class="mobile-links__item" data-collapse-item>
    <div class="mobile-links__item-title">
        <a href="{{ $href }}" class="mobile-links__item-link" @unless($isExternal) wire:navigate.hover @endunless>{{ $item->name }}</a>
    </div>
</li>
@endforeach