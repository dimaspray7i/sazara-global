@extends('layouts.app')
@section('title', $product->tr('name') . ' — Sazara Global')
@section('content')
<section class="section">
    <div class="container product-detail-container">
        <p class="meta back-link-wrap">
            <a class="back-link" href="{{ route('products.index') }}">&larr; {{ __('ui.common.back_products') }}</a>
        </p>
        <div class="section-head">
            <h2>{{ $product->tr('name') }}</h2>
            <div class="bar"></div>
        </div>
        <div class="product-showcase-img">
            <img src="{{ $product->imageUrl() }}" alt="{{ $product->tr('name') }}" loading="lazy">
        </div>
        <div class="product-detail-body">
            <p class="product-description">{{ $product->tr('description') }}</p>

            @if (filled($product->specification) || filled($product->specification_id))
                <div class="spec-box">
                    <h3 class="spec-title">{{ __('ui.common.specification') }}</h3>
                    <div class="spec-content">{!! nl2br(e($product->tr('specification'))) !!}</div>
                </div>
            @endif

            <div class="product-detail-actions">
                <a class="btn btn-secondary" data-wa="{{ $product->tr('name') }}" href="{{ $product->waUrl() }}" target="_blank" rel="noopener">{{ __('ui.common.get_offer_wa') }}</a>
                <a class="btn btn-outline" href="{{ route('contact') }}">{{ __('ui.nav.contact') }}</a>
            </div>
        </div>
    </div>
</section>
@endsection