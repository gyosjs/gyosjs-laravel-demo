@extends('layouts.app')

@section('content')
    <section class="section-head">
        <div><span class="eyebrow">36 seeded records · your session only</span><h2>Find the product that needs attention.</h2></div>
        <a class="button primary" href="{{ route('products.create') }}">+ Add product</a>
    </section>

    <form class="filters" action="{{ route('products.index') }}" method="get">
        <div class="field search-field"><label for="q">Search name or SKU</label><input class="input" id="q" name="q" value="{{ request('q') }}" placeholder="Try ‘Arc’ or ‘GY-FR’"></div>
        <div class="field"><label for="category">Category</label><select class="select" id="category" name="category"><option value="">All categories</option>@foreach ($categories as $category)<option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>@endforeach</select></div>
        <div class="field"><label for="status">Status</label><select class="select" id="status" name="status"><option value="">All statuses</option>@foreach ($statuses as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
        <div class="field"><label for="sort">Sort</label><select class="select" id="sort" name="sort"><option value="recent">Recently updated</option><option value="name" @selected(request('sort') === 'name')>Name A–Z</option><option value="price_high" @selected(request('sort') === 'price_high')>Price high</option><option value="stock_low" @selected(request('sort') === 'stock_low')>Stock low</option></select></div>
        <button class="button dark" type="submit" g-router-spin>Apply filters</button>
    </form>

    @include('products.partials.grid', ['appendResponse' => false])
@endsection
