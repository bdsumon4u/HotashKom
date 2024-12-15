@foreach ($categories as $category)
    <li id="menu-item-{{ $category->id }}"
        class="menu-item menu-item-type-taxonomy menu-item-object-product_cat @if($category->childrens) menu-item-has-children @endif menu-item-{{$category->id}} item-level-0">
        <a href="{{ route('products.index', ['filter_category' => $category->slug]) }}" class="nav-link"><span>{{ $category->name }}</span></a>
        <ul class="sub-menu">
            @foreach ($category->childrens as $category)
                <li id="menu-item-{{ $category->id }}"
                    class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-{{$category->id}} item-level-1">
                    <a href="{{ route('products.index', ['filter_category' => $category->slug]) }}" class="nav-link"><span>{{ $category->name }}</span></a>
                </li>
            @endforeach
        </ul>
    </li>
@endforeach
@foreach (\App\Models\Menu::whereSlug('header-menu')->first()?->menuItems ?? [] as $menuItem)
    <li id="menu-item-{{ $menuItem->id }}"
        class="menu-item menu-item-type-taxonomy menu-item-object-product_cat @if($menuItem->children ?? []) menu-item-has-children @endif menu-item-{{$menuItem->id}} item-level-0">
        <a href="{{ url($menuItem->href) }}" class="nav-link"><span>{{ $menuItem->name }}</span></a>
        <ul class="sub-menu">
            @foreach ($menuItem->children ?? [] as $menuItem)
                <li id="menu-item-{{ $menuItem->id }}"
                    class="menu-item menu-item-type-taxonomy menu-item-object-product_cat menu-item-{{$menuItem->id}} item-level-1">
                    <a href="{{ url($menuItem->href) }}" class="nav-link"><span>{{ $menuItem->name }}</span></a>
                </li>
            @endforeach
        </ul>
    </li>
@endforeach
