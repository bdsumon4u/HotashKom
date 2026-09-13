<div class="p-3 filter-sidebar" x-data="filterSidebar(@json(($attributes ?? collect())->pluck('id')))">
        <div class="filter-sidebar__header mb-3 pb-2 border-bottom d-flex justify-content-between align-items-center">
            <h3 class="filter-sidebar__title mb-0 d-flex align-items-center" style="font-size: 16px; font-weight: 800; color: #0f172a;">
                <i class="fas fa-sliders-h mr-2" style="color: var(--brand);"></i>
                <span>Filters</span>
            </h3>
            <button type="button" class="filter-sidebar__toggle d-md-none btn btn-sm btn-light" @click="mobileOpen = !mobileOpen" style="border-radius: 4px;">
                <i class="fa" :class="mobileOpen ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>
        </div>

        <form method="GET" action="{{
            $categorySlug
                ? route('category.show', $categorySlug)
                : ($brandSlug
                    ? route('brand.show', $brandSlug)
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
            <div class="filter-block mb-3">
                <div class="filter-block__header py-2 d-flex justify-content-between align-items-center" @click="categoriesOpen = !categoriesOpen" style="cursor: pointer;">
                    <h4 class="filter-block__title mb-0 font-weight-bold text-dark" style="font-size: 14px;">Categories</h4>
                    <i class="fa text-muted small" :class="categoriesOpen ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </div>
                <div class="filter-block__content mt-2" x-show="categoriesOpen" x-transition>
                    @forelse($categories ?? [] as $category)
                        <div class="filter-item mb-2">
                            <label class="filter-checkbox d-flex align-items-center justify-content-between mb-0 py-1 px-2" style="border-radius: 4px; cursor: pointer; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                <span class="d-flex align-items-center">
                                    <input type="checkbox"
                                           name="filter_category[]"
                                           value="{{ $category->id }}"
                                           class="mr-2"
                                           @if(in_array((int)$category->id, $selectedCategories)) checked @endif
                                           @change="updateFilter()">
                                    <span class="filter-checkbox__label text-dark" style="font-size: 13px; font-weight: 500;">{{ $category->name }}</span>
                                </span>
                                <span class="filter-checkbox__count badge badge-light font-weight-bold" style="font-size: 11px; color: #64748b; background: #f1f5f9; border-radius: 3px;">{{ $category->product_count ?? 0 }}</span>
                            </label>
                            @if($category->childrens->isNotEmpty())
                                <div class="ml-3 mt-1 pl-2 border-left filter-item__children">
                                    @foreach($category->childrens as $child)
                                        <label class="filter-checkbox d-flex align-items-center justify-content-between mb-0 py-1 px-2" style="border-radius: 4px; cursor: pointer; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                            <span class="d-flex align-items-center">
                                                <input type="checkbox"
                                                       name="filter_category[]"
                                                       value="{{ $child->id }}"
                                                       class="mr-2"
                                                       @if(in_array((int)$child->id, $selectedCategories)) checked @endif
                                                       @change="updateFilter()">
                                                <span class="filter-checkbox__label text-muted" style="font-size: 13px;">{{ $child->name }}</span>
                                            </span>
                                            <span class="filter-checkbox__count badge badge-light" style="font-size: 11px; color: #94a3b8; background: #f1f5f9; border-radius: 3px;">{{ $child->product_count ?? 0 }}</span>
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
                <div class="filter-block mb-3 border-top pt-2">
                    <div class="filter-block__header py-2 d-flex justify-content-between align-items-center" @click="attributesOpen['{{ $attribute->id }}'] = !attributesOpen['{{ $attribute->id }}']" style="cursor: pointer;">
                        <h4 class="filter-block__title mb-0 font-weight-bold text-dark" style="font-size: 14px;">{{ $attribute->name }}</h4>
                        <i class="fa text-muted small" :class="attributesOpen['{{ $attribute->id }}'] ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </div>
                    <div class="filter-block__content mt-2" x-show="attributesOpen['{{ $attribute->id }}']" x-transition>
                        @foreach($attribute->options as $option)
                            <label class="filter-checkbox d-flex align-items-center py-1 px-2 mb-1" style="border-radius: 4px; cursor: pointer; transition: background 0.15s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                <input type="checkbox"
                                       name="filter_option[]"
                                       value="{{ $option->id }}"
                                       class="mr-2"
                                       @if(in_array((int)$option->id, $selectedOptions)) checked @endif
                                       @change="updateFilter()">
                                <span class="filter-checkbox__label text-dark" style="font-size: 13px;">{{ $option->name }}</span>
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
            <div class="filter-actions d-flex gap-2 mt-3 pt-3 border-top">
                <button type="submit" class="btn btn-primary flex-fill font-weight-bold py-2" style="border-radius: 4px; font-size: 13px;">Filter</button>
                <a href="{{
                    $categorySlug
                        ? route('category.show', $categorySlug)
                        : ($brandSlug
                            ? route('brand.show', $brandSlug)
                            : route('products.index', ($search && $search !== '') ? ['search' => $search] : []))
                }}" class="btn btn-secondary flex-fill font-weight-bold py-2" style="border-radius: 4px; font-size: 13px;">Reset</a>
            </div>
        </form>
</div>
