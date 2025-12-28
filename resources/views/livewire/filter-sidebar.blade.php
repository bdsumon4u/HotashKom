<div class="p-3 filter-sidebar" x-data="filterSidebar(@json(($attributes ?? collect())->pluck('id')))">
        <div class="filter-sidebar__header">
            <h3 class="filter-sidebar__title">Filters</h3>
            <button type="button" class="filter-sidebar__toggle d-md-none" @click="mobileOpen = !mobileOpen">
                <i class="fa" :class="mobileOpen ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>
        </div>

        <form method="GET" action="{{
            $categorySlug
                ? route('categories.products', $categorySlug)
                : ($brandSlug
                    ? route('brands.products', $brandSlug)
                    : route('products.index'))
        }}" id="filter-form"
              x-show="mobileOpen || isDesktop"
              x-transition
              class="filter-sidebar__content"
              x-init="checkDesktop()">

            <!-- Preserve search parameter -->
            @if($search && $search !== '')
                <input type="hidden" name="search" value="{{ $search }}">
            @endif

            <!-- Categories Filter -->
            @if(!$hideCategoryFilter)
            <div class="filter-block">
                <div class="filter-block__header" @click="categoriesOpen = !categoriesOpen">
                    <h4 class="filter-block__title">Categories</h4>
                    <i class="fa" :class="categoriesOpen ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </div>
                <div class="filter-block__content" x-show="categoriesOpen" x-transition>
                    @forelse($categories ?? [] as $category)
                        <div class="filter-item">
                            <label class="filter-checkbox">
                                <input type="checkbox"
                                       name="filter_category[]"
                                       value="{{ $category->id }}"
                                       @if(in_array((int)$category->id, $selectedCategories)) checked @endif
                                       @change="updateFilter()">
                                <span class="filter-checkbox__label">{{ $category->name }}</span>
                                <span class="filter-checkbox__count">({{ $category->product_count ?? 0 }})</span>
                            </label>
                            @if($category->childrens->isNotEmpty())
                                <div class="ml-3 filter-item__children">
                                    @foreach($category->childrens as $child)
                                        <label class="filter-checkbox">
                                            <input type="checkbox"
                                                   name="filter_category[]"
                                                   value="{{ $child->id }}"
                                                   @if(in_array((int)$child->id, $selectedCategories)) checked @endif
                                                   @change="updateFilter()">
                                            <span class="filter-checkbox__label">{{ $child->name }}</span>
                                            <span class="filter-checkbox__count">({{ $child->product_count ?? 0 }})</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="mb-3 text-muted small">No categories available for filter.</p>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Attributes Filter -->
            @foreach($attributes ?? [] as $attribute)
                <div class="filter-block">
                    <div class="filter-block__header" @click="attributesOpen['{{ $attribute->id }}'] = !attributesOpen['{{ $attribute->id }}']">
                        <h4 class="filter-block__title">{{ $attribute->name }}</h4>
                        <i class="fa" :class="attributesOpen['{{ $attribute->id }}'] ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </div>
                    <div class="filter-block__content" x-show="attributesOpen['{{ $attribute->id }}']" x-transition>
                        @foreach($attribute->options as $option)
                            <label class="filter-checkbox">
                                <input type="checkbox"
                                       name="filter_option[]"
                                       value="{{ $option->id }}"
                                       @if(in_array((int)$option->id, $selectedOptions)) checked @endif
                                       @change="updateFilter()">
                                <span class="filter-checkbox__label">{{ $option->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach


            @php
                $hasCategoryFilters = !$hideCategoryFilter && collect($categories ?? [])->isNotEmpty();
                $hasAttributeFilters = collect($attributes ?? [])->isNotEmpty();
            @endphp

            @if(! $hasCategoryFilters && ! $hasAttributeFilters)
                <p class="mb-3 text-muted small">No attribute found for filter.</p>
            @endif

            <!-- Filter Actions -->
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{
                    $categorySlug
                        ? route('categories.products', $categorySlug)
                        : ($brandSlug
                            ? route('brands.products', $brandSlug)
                            : route('products.index', ($search && $search !== '') ? ['search' => $search] : []))
                }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
</div>
