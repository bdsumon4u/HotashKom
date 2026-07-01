<li class="mobile-links__item mobile-links__item--open" data-collapse-item>
    <div class="mobile-links__item-title">
        <a href="javascript:void(false)" class="mobile-links__item-link mobile-links__item-toggle" data-collapse-trigger aria-label="Toggle categories menu">Categories</a>
        <button class="mobile-links__item-toggle" type="button" data-collapse-trigger aria-label="Toggle categories menu">
            <svg class="mobile-links__item-arrow" width="12px" height="7px" viewBox="0 0 12 7"><path d="M.286.273a.92.92 0 0 0-.01 1.292l5.24 5.428 5.241-5.428a.92.92 0 0 0-.01-1.292.923.923 0 0 0-1.31.006L5.516 4.296 1.596.279A.923.923 0 0 0 .286.273z"/></svg>
        </button>
    </div>
    <div class="mobile-links__item-sub-links" data-collapse-content>
        <ul class="mobile-links mobile-links--level--1">
            @foreach($categories as $category)
                <li class="mobile-links__item" data-collapse-item>
                    <div class="mobile-links__item-title">
                        <a href="{{ route('categories.products', $category) }}" class="mobile-links__item-link" wire:navigate.hover>{{ $category->name }}</a>
                        @if($category->childrens->isNotEmpty())
                            <button class="mobile-links__item-toggle" type="button" data-collapse-trigger aria-label="Toggle {{ $category->name }} subcategories">
                                <svg class="mobile-links__item-arrow" width="12px" height="7px" viewBox="0 0 12 7"><path d="M.286.273a.92.92 0 0 0-.01 1.292l5.24 5.428 5.241-5.428a.92.92 0 0 0-.01-1.292.923.923 0 0 0-1.31.006L5.516 4.296 1.596.279A.923.923 0 0 0 .286.273z"/></svg>
                            </button>
                        @endif
                    </div>
                    @if($category->childrens->isNotEmpty())
                        <div class="mobile-links__item-sub-links" data-collapse-content>
                            <ul class="mobile-links mobile-links--level--2">
                                @foreach($category->childrens as $category)
                                    <li class="mobile-links__item" data-collapse-item>
                                        <div class="mobile-links__item-title">
                                            <a href="{{ route('categories.products', $category) }}" class="mobile-links__item-link" wire:navigate.hover>{{ $category->name }}</a>
                                            @if($category->childrens->isNotEmpty())
                                                <button class="mobile-links__item-toggle" type="button" data-collapse-trigger aria-label="Toggle {{ $category->name }} subcategories">
                                                    <svg class="mobile-links__item-arrow" width="12px" height="7px" viewBox="0 0 12 7"><path d="M.286.273a.92.92 0 0 0-.01 1.292l5.24 5.428 5.241-5.428a.92.92 0 0 0-.01-1.292.923.923 0 0 0-1.31.006L5.516 4.296 1.596.279A.923.923 0 0 0 .286.273z"/></svg>
                                                </button>
                                            @endif
                                        </div>
                                        @if($category->childrens->isNotEmpty())
                                            <div class="mobile-links__item-sub-links" data-collapse-content>
                                                <ul class="mobile-links mobile-links--level--2">
                                                    @foreach($category->childrens as $category)
                                                        <li class="mobile-links__item" data-collapse-item>
                                                            <div class="mobile-links__item-title">
                                                                <a href="{{ route('categories.products', $category) }}" class="mobile-links__item-link" wire:navigate.hover>{{ $category->name }}</a>
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
                    <a href="{{ route('categories') }}" class="mobile-links__item-link" wire:navigate.hover>View All Categories</a>
                    <a href="{{ route('categories') }}" class="mobile-links__item-toggle d-flex justify-content-center align-items-center" wire:navigate.hover aria-label="View All Categories">
                        <svg class="mobile-links__item-arrow" width="12px" height="12px" viewBox="0 0 8 13"><path d="M.3 11.4l5-4.9-5-4.9C-.1 1.2-.1.7.3.3s.9-.4 1.3 0L8 6.5l-6.4 6.2c-.4.4-.9.3-1.3 0s-.4-.9 0-1.3z"/></svg>
                    </a>
                </div>
            </li>
        </ul>
    </div>
</li>
