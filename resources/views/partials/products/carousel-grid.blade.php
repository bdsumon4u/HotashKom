<div class="block block-products-carousel" data-layout="grid-{{ $cols ?? 5 }}">
    <div class="container">
        <div class="section-title">
            <h2>{{ $title }}</h2>
        </div>

        <div class="products-grid-modern" data-cols="{{ $cols ?? 3 }}">
            @foreach($products as $product)
                @livewire('product-card', ['product' => $product], key($product->id))
            @endforeach
        </div>
    </div>
</div>
