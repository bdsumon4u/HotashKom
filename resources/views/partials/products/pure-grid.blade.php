<div class="block block-products-carousel">
    <div class="container">
        @if($title ?? null)
            <div class="section-title">
                <h2>{{ $title }}</h2>
            </div>
        @endif

        <div class="products-grid-modern" data-cols="{{ $cols ?? 3 }}">
            @foreach($products as $product)
                @livewire('product-card', ['product' => $product], key($product->id))
            @endforeach
        </div>
    </div>
</div>
