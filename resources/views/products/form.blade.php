@extends('layouts.app')

@php
    $initialStep = $errors->hasAny(['name', 'sku', 'category']) ? 1 : ($errors->hasAny(['price', 'status']) ? 2 : ($errors->any() ? 3 : 1));
@endphp

@section('content')
    <section class="section-head"><div><span class="eyebrow">POST → validation → redirect</span><h2>{{ $product->exists ? 'Tune the catalog record.' : 'Introduce a new product.' }}</h2></div><p>Steps are local reactive state. Every field and error still belongs to the Laravel form contract.</p></section>

    <form
        class="form-card"
        method="post"
        novalidate
        action="{{ $product->exists ? route('products.update', $product) : route('products.store') }}"
        g-scope="ProductForm"
        gd-step="{{ $initialStep }}"
        gd-name="{{ old('name', $product->name) }}"
        gd-sku="{{ old('sku', $product->sku) }}"
        gd-category="{{ old('category', $product->category) }}"
        gd-description="{{ old('description', $product->description) }}"
        gd-price="{{ old('price', $product->price ?? 0) }}"
        gd-status="{{ old('status', $product->status) }}"
        gd-stock="{{ old('stock', $product->stock ?? 0) }}"
    >
        @csrf
        @if ($product->exists) @method('put') @endif

        @if ($errors->any())
            <div class="error-summary"><strong>The server rejected this submission.</strong><span>Review the highlighted fields. Your previous input was preserved.</span></div>
        @endif

        <div class="stepper">
            <button class="step" :class="{ active: step === 1 }" type="button" @click="goTo(1)"><strong>01</strong> <span>Details</span></button>
            <button class="step" :class="{ active: step === 2 }" type="button" @click="goTo(2)"><strong>02</strong> <span>Pricing</span></button>
            <button class="step" :class="{ active: step === 3 }" type="button" @click="goTo(3)"><strong>03</strong> <span>Inventory</span></button>
        </div>

        <section g-show="step === 1" g-cloak>
            <div class="form-grid">
                <div class="field wide"><label for="name">Product name</label><input class="input" id="name" name="name" value="{{ old('name', $product->name) }}" g-model.trim="name" required>@error('name')<span class="field-error">{{ $message }}</span>@enderror</div>
                <div class="field"><label for="sku">SKU</label><input class="input" id="sku" name="sku" value="{{ old('sku', $product->sku) }}" g-model.trim="sku" required>@error('sku')<span class="field-error">{{ $message }}</span>@enderror</div>
                <div class="field"><label for="category">Category</label><select class="select" id="category" name="category" g-model="category" required><option value="">Choose category</option>@foreach ($categories as $category)<option value="{{ $category }}" @selected(old('category', $product->category) === $category)>{{ $category }}</option>@endforeach</select>@error('category')<span class="field-error">{{ $message }}</span>@enderror</div>
                <div class="field wide"><label for="description">Description</label><textarea class="textarea" id="description" name="description" g-model.trim="description">{{ old('description', $product->description) }}</textarea>@error('description')<span class="field-error">{{ $message }}</span>@enderror</div>
            </div>
        </section>

        <section g-show="step === 2" g-cloak>
            <div class="form-grid">
                <div class="field"><label for="price">Unit price (USD)</label><input class="input" id="price" name="price" type="number" step="0.01" value="{{ old('price', $product->price) }}" g-model.number="price" required>@error('price')<span class="field-error">{{ $message }}</span>@enderror</div>
                <div class="field"><label for="status">Publishing status</label><select class="select" id="status" name="status" g-model="status" required>@foreach ($statuses as $status)<option value="{{ $status }}" @selected(old('status', $product->status) === $status)>{{ ucfirst($status) }}</option>@endforeach</select>@error('status')<span class="field-error">{{ $message }}</span>@enderror</div>
            </div>
        </section>

        <section g-show="step === 3" g-cloak>
            <div class="form-grid">
                <div class="field"><label for="stock">Units in stock</label><input class="input" id="stock" name="stock" type="number" value="{{ old('stock', $product->stock) }}" g-model.number="stock" required>@error('stock')<span class="field-error">{{ $message }}</span>@enderror</div>
                <div class="field"><label>Reactive review</label><div class="panel" style="padding:12px"><strong>{name || 'Untitled product'}</strong><br><span class="meta">{category || 'No category'} · ${Number(price || 0).toFixed(2)} · {stock || 0} units</span><p>Laravel validates, updates the database, then redirects to the canonical detail URL.</p></div></div>
            </div>
        </section>

        <div class="form-actions"><button class="button ghost" type="button" @click="previous()" g-show="step > 1">← Previous</button><span></span><button class="button dark" type="button" @click="next()" g-show="step < 3">Next step →</button><button class="button primary" type="submit" g-show="step === 3" g-router-spin>{{ $product->exists ? 'Save product' : 'Create product' }}</button></div>
    </form>
@endsection
