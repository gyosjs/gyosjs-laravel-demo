@extends('layouts.app')

@section('content')
    <section class="section-head">
        <div><span class="eyebrow">Live server inventory</span><h2>A small app under real pressure.</h2></div>
        <p>Every number comes from Laravel. GyosJS keeps navigation, scopes, fragments, and persisted UI moving around that server-owned HTML.</p>
    </section>

    <div class="metric-grid">
        <article class="metric-card" data-index="01"><span class="label">Catalog entries</span><span class="metric-value">{{ $metrics['total'] }}</span></article>
        <article class="metric-card" data-index="02"><span class="label">Active products</span><span class="metric-value">{{ $metrics['active'] }}</span></article>
        <article class="metric-card" data-index="03"><span class="label">Low stock</span><span class="metric-value">{{ $metrics['lowStock'] }}</span></article>
        <article class="metric-card" data-index="04"><span class="label">Inventory value</span><span class="metric-value">${{ number_format($metrics['inventoryValue'], 0) }}</span></article>
    </div>

    <section class="panel">
        <header class="panel-head"><h3>Recently touched</h3><a class="button ghost" href="{{ route('products.index') }}">Open catalog →</a></header>
        @foreach ($recentProducts as $product)
            <a class="product-row" href="{{ route('products.show', $product) }}">
                <span class="product-art tone-{{ $product->image_tone }}"></span>
                <span><strong>{{ $product->name }}</strong><br><span class="meta">{{ $product->sku }}</span></span>
                <span>{{ $product->category }}</span>
                <span>${{ number_format($product->price, 2) }}</span>
                <span>{{ $product->stock }} units</span>
                <span class="badge {{ $product->status }}">{{ $product->status }}</span>
            </a>
        @endforeach
    </section>
@endsection
