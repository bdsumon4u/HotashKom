<div class="p-3 filter-sidebar" x-data="filterSidebar(@json(($attributes ?? collect())->pluck('id')))">
        <div class="filter-sidebar__header">
            <h3 class="filter-sidebar__title">Filters</h3>
            <button type="button" class="filter-sidebar__toggle d-md-none" @click="mobileOpen = !mobileOpen">
                <i class="fa" :class="mobileOpen ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>
        </div>

        <form method="GET" action="{{
            $categoryId
                ? route('categories.products', $categoryId)
                : ($brandId
                    ? route('brands.products', $brandId)
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
                    @php
                        $filterCategory = request('filter_category');
                        $selectedCategories = [];

                        if ($filterCategory) {
                            if (is_array($filterCategory)) {
                                $selectedCategories = array_map('intval', array_filter($filterCategory));
                            } elseif (is_numeric(str_replace(',', '', $filterCategory))) {
                                $selectedCategories = array_map('intval', explode(',', $filterCategory));
                            }
                        }
                    @endphp
                    @foreach($categories ?? [] as $category)
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
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Attributes Filter -->
            @php
                $filterOption = request('filter_option');
                $selectedOptions = [];

                if ($filterOption) {
                    if (is_array($filterOption)) {
                        $selectedOptions = array_map('intval', array_filter($filterOption));
                    } else {
                        $selectedOptions = array_map('intval', explode(',', $filterOption));
                    }
                }
            @endphp
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

            <!-- Filter Actions -->
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{
                    $categoryId
                        ? route('categories.products', $categoryId)
                        : ($brandId
                            ? route('brands.products', $brandId)
                            : route('products.index', ($search && $search !== '') ? ['search' => $search] : []))
                }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
</div>
