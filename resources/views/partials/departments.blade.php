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
                                    <svg class="departments__link-arrow" width="6px" height="9px">
                                        <use
                                            xlink:href="{{ asset('strokya/images/sprite.svg#arrow-rounded-right-6x9') }}">
                                        </use>
                                    </svg>
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
                                                        <svg class="menu__arrow" width="6px" height="9px">
                                                            <use
                                                                xlink:href="{{ asset('strokya/images/sprite.svg#arrow-rounded-right-6x9') }}">
                                                            </use>
                                                        </svg>
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
                            <svg class="departments__link-arrow" width="6px" height="9px">
                                <use
                                    xlink:href="{{ asset('strokya/images/sprite.svg#arrow-rounded-right-6x9') }}">
                                </use>
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <button class="departments__button">
            <svg class="departments__button-icon" width="18px" height="14px">
                <use
                    xlink:href="{{ asset('strokya/images/sprite.svg#menu-18x14') }}">
                </use>
            </svg>
            Shop By Category
            <svg class="departments__button-arrow" width="9px" height="6px">
                <use
                    xlink:href="{{ asset('strokya/images/sprite.svg#arrow-rounded-down-9x6') }}">
                </use>
            </svg>
        </button>
    </div><!-- .departments / end -->
</div>