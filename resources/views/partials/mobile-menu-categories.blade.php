<li class="mobile-links__item mobile-links__item--open" data-collapse-item>
    <div class="mobile-links__item-title">
        <a href="javascript:void(false)" class="mobile-links__item-link mobile-links__item-toggle" data-collapse-trigger>Categories</a>
        <button class="mobile-links__item-toggle" type="button" data-collapse-trigger>
            <svg class="mobile-links__item-arrow" width="12px" height="7px">
                <use xlink:href="{{ asset('strokya/images/sprite.svg#arrow-rounded-down-12x7') }}"></use>
            </svg>
        </button>
    </div>
    <div class="mobile-links__item-sub-links" data-collapse-content>
        <ul class="mobile-links mobile-links--level--1">
            @foreach($categories as $category)
                <li class="mobile-links__item" data-collapse-item>
                    <div class="mobile-links__item-title">
                        <a href="{{ route('categories.products', $category) }}" class="mobile-links__item-link">{{ $category->name }}</a>
                        @if($category->childrens->isNotEmpty())
                            <button class="mobile-links__item-toggle" type="button" data-collapse-trigger>
                                <svg class="mobile-links__item-arrow" width="12px" height="7px">
                                    <use xlink:href="{{ asset('strokya/images/sprite.svg#arrow-rounded-down-12x7') }}"></use>
                                </svg>
                            </button>
                        @endif
                    </div>
                    @if($category->childrens->isNotEmpty())
                        <div class="mobile-links__item-sub-links" data-collapse-content>
                            <ul class="mobile-links mobile-links--level--2">
                                @foreach($category->childrens as $category)
                                    <li class="mobile-links__item" data-collapse-item>
                                        <div class="mobile-links__item-title">
                                            <a href="{{ route('categories.products', $category) }}" class="mobile-links__item-link">{{ $category->name }}</a>
                                            @if($category->childrens->isNotEmpty())
                                                <button class="mobile-links__item-toggle" type="button" data-collapse-trigger>
                                                    <svg class="mobile-links__item-arrow" width="12px" height="7px">
                                                        <use xlink:href="{{ asset('strokya/images/sprite.svg#arrow-rounded-down-12x7') }}"></use>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                        @if($category->childrens->isNotEmpty())
                                            <div class="mobile-links__item-sub-links" data-collapse-content>
                                                <ul class="mobile-links mobile-links--level--2">
                                                    @foreach($category->childrens as $category)
                                                        <li class="mobile-links__item" data-collapse-item>
                                                            <div class="mobile-links__item-title">
                                                                <a href="{{ route('categories.products', $category) }}" class="mobile-links__item-link">{{ $category->name }}</a>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </li>
            @endforeach
            <li class="mobile-links__item">
                <div class="mobile-links__item-title">
                    <a href="{{ route('categories') }}" class="mobile-links__item-link">View All Categories</a>
                    <a href="{{ route('categories') }}" class="mobile-links__item-toggle d-flex justify-content-center align-items-center">
                        <svg class="mobile-links__item-arrow" width="12px" height="12px">
                            <use xlink:href="{{ asset('strokya/images/sprite.svg#arrow-rounded-right-8x13') }}"></use>
                        </svg>
                    </a>
                </div>
            </li>
        </ul>
    </div>
</li>
