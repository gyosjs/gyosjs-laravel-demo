@extends('layouts.app')

@section('content')
    <div class="detail-grid">
        <div class="product-art tone-{{ $product->image_tone }}"></div>
        <section>
            <span class="eyebrow">{{ $product->category }} / {{ $product->sku }}</span>
            <h2 class="detail-title">{{ $product->name }}</h2>
            <span class="badge {{ $product->status }}">{{ $product->status }}</span>
            <div class="detail-price">${{ number_format($product->price, 2) }}</div>
            <p>{{ $product->description }}</p>
            <div class="detail-list"><div class="detail-line"><span>Current stock</span><strong>{{ $product->stock }} units</strong></div><div class="detail-line"><span>Last server update</span><strong>{{ $product->updated_at->diffForHumans() }}</strong></div></div>
            <div class="detail-actions">
                <a class="button primary" href="{{ route('products.edit', $product) }}">Edit this product</a>
                <a class="button" href="{{ route('products.index') }}">Back to catalog</a>
                <form method="post" action="{{ route('products.destroy', $product) }}" g-scope="ConfirmAction" gd-message="Remove this product from your demo workspace?" @submit="confirm($event)">@csrf @method('delete')<button class="button danger" type="submit">Delete</button></form>
            </div>
        </section>
    </div>
@endsection
