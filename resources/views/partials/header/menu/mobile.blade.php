@foreach($menuItems as $item)
<li class="mobile-links__item" data-collapse-item>
    <div class="mobile-links__item-title">
        <a href="{{ url($item->href) }}" class="mobile-links__item-link">{{ $item->name }}</a>
    </div>
</li>
@endforeach