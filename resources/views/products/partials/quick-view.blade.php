<div id="modal-shell">
    <div class="modal-overlay" g-scope="{ open: true }" g-show="open" g-transition>
        <article class="modal" @click.outside="open = false">
            <header class="modal-head"><span class="eyebrow">Server-rendered fragment</span><button class="icon-button" type="button" @click="open = false" aria-label="Close modal">×</button></header>
            <div class="modal-body">
                <div class="product-art tone-{{ $product->image_tone }}"></div>
                <div><span class="badge {{ $product->status }}">{{ $product->status }}</span><h2>{{ $product->name }}</h2><p>{{ $product->description }}</p><strong class="detail-price">${{ number_format($product->price, 2) }}</strong><p class="meta">{{ $product->sku }} · {{ $product->stock }} units</p><a class="button primary" href="{{ route('products.edit', $product) }}">Edit product</a></div>
            </div>
        </article>
    </div>
</div>
