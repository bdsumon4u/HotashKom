<div class="block block-products-carousel" data-layout="grid-{{ $cols ?? 5 }}">
    <div class="container">
        <div class="block-header">
            <h3 class="block-header__title" style="padding: 0.375rem 1rem;">
                @isset($section)
                    <a href="{{ route('home-sections.products', $section) }}">{{ $title }}</a>
                @else
                    {{ $title }}
                @endisset
            </h3>
            <div class="block-header__divider"></div>
            <div class="block-header__arrows-list">
                <button class="block-header__arrow block-header__arrow--left" type="button">
                    <svg width="7px" height="11px">
                        <use xlink:href="{{ asset('strokya/images/sprite.svg#arrow-rounded-left-7x11') }}"></use>
                    </svg>
                </button>
                <button class="block-header__arrow block-header__arrow--right" type="button">
                    <svg width="7px" height="11px">
                        <use xlink:href="{{ asset('strokya/images/sprite.svg#arrow-rounded-right-7x11') }}"></use>
                    </svg>
                </button>
            </div>
        </div>
        <div class="block-products-carousel__slider">
            <div class="block-products-carousel__preloader"></div>
            <div class="owl-carousel">
                @foreach($products->chunk($rows ?? 2) as $products)
                <div class="block-products-carousel__column">
                    @foreach($products as $product)
                    <div class="block-products-carousel__cell">
                        <livewire:product-card :product="$product" :key="$product->id" />
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
