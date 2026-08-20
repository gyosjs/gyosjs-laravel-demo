@extends('layouts.app')

@section('content')
    <section class="section-head">
        <div>
            <span class="eyebrow">Reactive counts · server-confirmed update</span>
            <h2>Count what needs attention.</h2>
        </div>
        <p>Change several counts, review the variance locally, then let Laravel validate and commit the complete stocktake.</p>
    </section>

    <form
        class="stocktake-card"
        method="post"
        action="{{ route('stocktake.update') }}"
        g-scope="Stocktake"
        g-form="stocktakeForm"
        gd-expected='@json($expected)'
        gd-counts='@json($counts)'
    >
        @csrf

        <div class="stocktake-toolbar">
            <div>
                <strong>{changedCount} lines changed</strong>
                <span class="meta">{totalVariance > 0 ? '+' : ''}{totalVariance} units overall</span>
            </div>
            <div class="stocktake-summary" g-show="changedCount > 0" g-transition="fade" g-cloak>
                <span>Review before saving</span>
                <button class="button primary" type="submit" :disabled="changedCount === 0" g-router-spin>Save stocktake</button>
            </div>
        </div>

        <div class="stocktake-list">
            @foreach ($products as $product)
                <article
                    class="stocktake-row"
                    g-reveal
                    :class="{ 'is-changed': isChanged({{ $product->id }}), 'is-positive': variance({{ $product->id }}) > 0, 'is-negative': variance({{ $product->id }}) < 0 }"
                    :data-variance="variance({{ $product->id }})"
                    data-product-id="{{ $product->id }}"
                >
                    <div class="product-art tone-{{ $product->image_tone }}"></div>
                    <div class="stocktake-product">
                        <strong>{{ $product->name }}</strong>
                        <span class="meta">{{ $product->sku }} · system says {{ $product->stock }}</span>
                    </div>
                    <div class="stocktake-number">
                        <label for="count-{{ $product->id }}">Counted stock</label>
                        <input
                            class="input"
                            id="count-{{ $product->id }}"
                            name="counts[{{ $product->id }}]"
                            type="number"
                            min="0"
                            max="1000000"
                            value="{{ $product->stock }}"
                            g-model.number="counts[{{ $product->id }}]"
                            g-validate="required"
                            :aria-invalid="hasError({{ $product->id }})"
                            :aria-describedby="'variance-' + {{ $product->id }}"
                            required
                        >
                        <span class="field-error" id="variance-{{ $product->id }}" g-errors="counts[{{ $product->id }}]"></span>
                    </div>
                    <div class="stocktake-variance" aria-live="polite">
                        <span class="meta">Variance</span>
                        <strong>{variance({{ $product->id }}) > 0 ? '+' : ''}{variance({{ $product->id }})}</strong>
                    </div>
                </article>
            @endforeach
        </div>

        @if ($errors->any())
            <div class="error-summary"><strong>The server rejected this stocktake.</strong><span>Review the highlighted counts and try again.</span></div>
        @endif

        <div class="stocktake-footer">
            <span class="meta">Only the twelve lowest-stock products are shown.</span>
            <button class="button primary" type="submit" :disabled="changedCount === 0" g-router-spin>Save stocktake</button>
        </div>
    </form>
@endsection
