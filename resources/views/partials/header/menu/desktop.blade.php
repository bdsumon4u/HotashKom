<div class="nav-panel__nav-links nav-links">
    <ul class="nav-links__list">
        @foreach($categories as $category)
        <li class="nav-links__item @if($category->childrens->isNotEmpty()) nav-links__item--has-submenu @endif">
            <a href="{{route('categories.products', $category)}}" aria-current="page" class="nav-links__item-link">
                <div class="nav-links__item-body">
                    {{ $category->name }}
                    @if($category->childrens->isNotEmpty())
                    <svg xmlns="http://www.w3.org/2000/svg" width="9" height="6" class="nav-links__item-arrow"><path d="M.2.4c.4-.4 1-.5 1.4-.1l2.9 3 2.9-3c.4-.4 1.1-.4 1.4.1.3.4.3.9-.1 1.3L4.5 6 .3 1.6C-.1 1.3-.1.7.2.4z"></path></svg>
                    @endif
                </div>
            </a>
            @if($category->childrens->isNotEmpty())
            <div class="nav-links__submenu nav-links__submenu--type--menu" style="width: 220px;">
                <ul class="departments__links">
                    @foreach($category->childrens as $category)
                    <li class="departments__item ">
                        <a href="{{ route('categories.products', $category) }}">{{ $category->name }}</a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </li>
        @endforeach
        @foreach($menuItems as $item)
        <li class="nav-links__item">
            <a href="{{ url($item->href) }}">
                <span>{{ $item->name }}</span>
            </a>
        </li>
        @endforeach
    </ul>
</div>