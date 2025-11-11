@extends('layouts.yellow.master')

@section('title', 'Products')

@section('content')

@include('partials.page-header', [
    'paths' => [
        url('/') => 'Home',
    ],
    'active' => 'Products',
    'page_title' => 'Products'
])

<div class="block">
    <div class="products-view">
        <div class="container">
            <div class="row">
                <!-- Filter Sidebar -->
                <div class="pr-md-1 col-lg-3 col-md-4" x-data="filterSidebar()">
                    <div class="p-3 filter-sidebar">
                        <div class="filter-sidebar__header">
                            <h3 class="filter-sidebar__title">Filters</h3>
                            <button type="button" class="filter-sidebar__toggle d-md-none" @click="mobileOpen = !mobileOpen">
                                <i class="fa" :class="mobileOpen ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                            </button>
                        </div>

                        <form method="GET" action="{{
                            ($category = request()->route()->parameter('category'))
                                ? route('categories.products', $category)
                                : (($brand = request()->route()->parameter('brand'))
                                    ? route('brands.products', $brand)
                                    : route('products.index'))
                        }}" id="filter-form"
                              x-show="mobileOpen || isDesktop"
                              x-transition
                              class="filter-sidebar__content"
                              x-init="checkDesktop()">

                            <!-- Preserve search parameter -->
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif

                            <!-- Categories Filter -->
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
                                                <span class="filter-checkbox__count">({{ $category->products()->whereIsActive(1)->whereNull('parent_id')->count() }})</span>
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
                                                            <span class="filter-checkbox__count">({{ $child->products()->whereIsActive(1)->whereNull('parent_id')->count() }})</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>

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
                                    ($category = request()->route()->parameter('category'))
                                        ? route('categories.products', $category)
                                        : (($brand = request()->route()->parameter('brand'))
                                            ? route('brands.products', $brand)
                                            : route('products.index', request()->only('search')))
                                }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Products Content -->
                <div class="pl-md-1 col-lg-9 col-md-8">
                    <div class="products-view__options">
                        <div class="view-options">
                            <div class="view-options__legend">
                                @if(request('search'))
                                Found {{ $products->total() }} result(s) for "{{ request('search', 'NULL') }}"
                                @elseif($category = request()->route()->parameter('category'))
                                Showing from "{{ $category->name }}" category.
                                @elseif($brand = request()->route()->parameter('brand'))
                                Showing from "{{ $brand->name }}" brand.
                                @else
                                Showing {{ $products->count() }} of {{ $products->total() }} products
                                @endif
                            </div>
                            <div class="view-options__divider"></div>
                        </div>
                    </div>

                    @include('partials.products.pure-grid', [
                        'title' => null,
                        'cols' => 4,
                    ])

                    <div class="pt-0 products-view__pagination">
                        {!! $products->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.filter-sidebar {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1.5rem;
    position: sticky;
    top: 20px;
    max-height: calc(100vh - 40px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.filter-sidebar__content {
    overflow-y: auto;
    overflow-x: hidden;
    flex: 1;
    padding-right: 0.5rem;
    margin-right: -0.5rem;
}

.filter-sidebar__content::-webkit-scrollbar {
    width: 6px;
}

.filter-sidebar__content::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.filter-sidebar__content::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.filter-sidebar__content::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.filter-sidebar__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
}

.filter-sidebar__title {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0;
}

.filter-sidebar__toggle {
    background: none;
    border: none;
    font-size: 1.2rem;
    color: #6c757d;
}

.filter-block {
    border-bottom: 1px solid #e9ecef;
}

.filter-block:last-child {
    border-bottom: none;
}

.filter-block__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    padding: 0.5rem 0;
}

.filter-block__title {
    font-size: 1rem;
    font-weight: 600;
    margin: 0;
}

.filter-block__content {
    margin-top: 0.5rem;
}

.filter-item {
    margin-bottom: 0.75rem;
}

.filter-item__children {
    margin-top: 0.5rem;
}

.filter-checkbox {
    display: flex;
    align-items: center;
    cursor: pointer;
    padding: 0;
    user-select: none;
}

.filter-checkbox input[type="checkbox"] {
    margin-right: 0.5rem;
    cursor: pointer;
}

.filter-checkbox__label {
    flex: 1;
    color: #333;
}

.filter-checkbox__count {
    color: #6c757d;
    font-size: 0.9rem;
}

.filter-actions {
    margin-top: 1.5rem;
    display: flex;
    gap: 0.5rem;
}

.filter-actions .btn {
    flex: 1;
}

@media (max-width: 767px) {
    .filter-sidebar {
        position: relative;
        top: 0;
        margin-bottom: 1rem;
        max-height: none;
    }

    .filter-sidebar__content {
        max-height: 70vh;
    }
}
</style>
@endpush

@push('scripts')
<script>
function filterSidebar() {
    return {
        mobileOpen: false,
        isDesktop: window.innerWidth >= 768,
        categoriesOpen: true,
        attributesOpen: {},

        init() {
            // Initialize attributes open state
            @foreach($attributes ?? [] as $attribute)
                this.attributesOpen['{{ $attribute->id }}'] = true;
            @endforeach

            // Handle window resize
            window.addEventListener('resize', () => {
                this.checkDesktop();
            });
        },

        checkDesktop() {
            this.isDesktop = window.innerWidth >= 768;
            if (this.isDesktop) {
                this.mobileOpen = true;
            }
        },

        updateFilter() {
            // Auto-submit on filter change (optional - remove if you want manual filter button)
            // this.$el.closest('form').submit();
        }
    }
}
</script>
@endpush
@endsection
