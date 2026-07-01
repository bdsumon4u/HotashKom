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
                    <svg width="7px" height="11px" viewBox="0 0 7 11"><path d="M6.7.3c-.4-.4-.9-.4-1.3 0L0 5.5l5.4 5.2c.4.4.9.3 1.3 0 .4-.4.4-1 0-1.3l-4-3.9 4-3.9c.4-.4.4-1 0-1.3z"/></svg>
                </button>
                <button class="block-header__arrow block-header__arrow--right" type="button">
                    <svg width="7px" height="11px" viewBox="0 0 7 11"><path d="M.3 10.7c.4.4.9.4 1.3 0L7 5.5 1.6.3C1.2-.1.7 0 .3.3c-.4.4-.4 1 0 1.3l4 3.9-4 3.9c-.4.4-.4 1 0 1.3z"/></svg>
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
