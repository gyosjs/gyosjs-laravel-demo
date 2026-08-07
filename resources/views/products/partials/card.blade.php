<article class="product-card" data-product-id="{{ $product->id }}">
    <a class="product-art tone-{{ $product->image_tone }}" href="{{ route('products.show', $product) }}" aria-label="Open {{ $product->name }}"></a>
    <div class="product-card-body">
        <div class="product-card-meta"><span>{{ $product->category }}</span><span>{{ $product->stock }} in stock</span></div>
        <h3><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></h3>
        <span class="badge {{ $product->status }}">{{ $product->status }}</span>
        <div class="product-card-actions">
            <a class="button ghost" href="{{ route('products.quick-view', $product) }}" g-target="#modal-shell" g-swap="inner" g-current-state>Quick view</a>
            <a class="button" href="{{ route('products.edit', $product) }}">Edit · ${{ number_format($product->price, 0) }}</a>
        </div>
    </div>
</article>
