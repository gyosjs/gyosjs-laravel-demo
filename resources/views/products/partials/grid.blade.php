<div id="product-grid" class="product-grid">
    @forelse ($products as $product)
        @include('products.partials.card', ['product' => $product])
    @empty
        <div class="empty-state"><strong>No products match this filter.</strong><p>Change the query or reset the demo workspace.</p></div>
    @endforelse

    @if ($products->hasMorePages())
        <a
            class="button dark load-more"
            href="{{ route('products.index', array_merge(request()->query(), ['page' => $products->currentPage() + 1])) }}"
            g-router-link="{{ route('products.load-more', array_merge(request()->query(), ['page' => $products->currentPage() + 1])) }}"
            g-router-method="GET"
            g-target="#product-grid"
            g-swap="append"
            g-noscroll
            g-router-spin
            g-router-remove
        >Load 12 more</a>
    @endif
</div>
