<div class="nav-panel__departments">
    <!-- .departments -->
    @php $fixed = request()->is('/') && (setting('show_option')->category_dropdown ?? false); @endphp
    <div
        class="departments {{ $fixed ? 'departments--opened departments--fixed' : '' }}"
        data-departments-fixed-by="{{ $fixed ? '.block-slideshow' : '' }}">
        <div class="departments__body">
            <div class="departments__links-wrapper">
                <ul class="departments__links">
                    @foreach($categories as $category)
                        <li class="departments__item @if($category->childrens->isNotEmpty()) departments__item--menu @endif">
                            <a href="{{ route('categories.products', $category) }}" wire:navigate.hover>{{ $category->name }}
                                @if ($category->childrens->isNotEmpty())
                                    <svg class="departments__link-arrow" width="6px" height="9px" viewBox="0 0 6 9"><path d="M.4 8.8c-.4-.4-.5-1-.1-1.4l3-2.9-3-2.9C-.1 1.2-.1.5.4.2c.4-.3.9-.3 1.3.1L6 4.5 1.6 8.7c-.3.4-.9.4-1.2.1z"/></svg>
                                @endif
                            </a>
                            @if($category->childrens->isNotEmpty())
                                <div class="departments__menu">
                                    <!-- .menu -->
                                    <ul class="menu menu--layout--classic">
                                        @foreach ($category->childrens as $category)
                                            <li>
                                                <a href="{{ route('categories.products', $category) }}" wire:navigate.hover>{{ $category->name }}
                                                    @if ($category->childrens->isNotEmpty())
                                                        <svg class="menu__arrow" width="6px" height="9px" viewBox="0 0 6 9"><path d="M.4 8.8c-.4-.4-.5-1-.1-1.4l3-2.9-3-2.9C-.1 1.2-.1.5.4.2c.4-.3.9-.3 1.3.1L6 4.5 1.6 8.7c-.3.4-.9.4-1.2.1z"/></svg>
                                                    @endif
                                                </a>
                                                @if($category->childrens->isNotEmpty())
                                                    <div class="menu__submenu">
                                                        <!-- .menu -->
                                                        <ul class="menu menu--layout--classic">
                                                            @foreach($category->childrens as $category)
                                                                <li><a href="{{ route('categories.products', $category) }}" wire:navigate.hover>{{ $category->name }}</a></li>
                                                            @endforeach
                                                        </ul>
                                                        <!-- .menu / end -->
                                                    </div>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul><!-- .menu / end -->
                                </div>
                            @endif
                        </li>
                    @endforeach
                    <li class="departments__item">
                        <a href="{{ route('categories') }}" wire:navigate.hover>View All Categories
                            <svg class="departments__link-arrow" width="6px" height="9px" viewBox="0 0 6 9"><path d="M.4 8.8c-.4-.4-.5-1-.1-1.4l3-2.9-3-2.9C-.1 1.2-.1.5.4.2c.4-.3.9-.3 1.3.1L6 4.5 1.6 8.7c-.3.4-.9.4-1.2.1z"/></svg>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <button class="departments__button">
            <svg class="departments__button-icon" width="18px" height="14px" viewBox="0 0 18 14"><path d="M0 8V6h18v2H0zm0-8h18v2H0V0zm14 14H0v-2h14v2z"/></svg>
            Shop By Category
            <svg class="departments__button-arrow" width="9px" height="6px" viewBox="0 0 9 6"><path d="M.2.4c.4-.4 1-.5 1.4-.1l2.9 3 2.9-3c.4-.4 1.1-.4 1.4.1.3.4.3.9-.1 1.3L4.5 6 .3 1.6C-.1 1.3-.1.7.2.4z"/></svg>
        </button>
    </div><!-- .departments / end -->
</div>
