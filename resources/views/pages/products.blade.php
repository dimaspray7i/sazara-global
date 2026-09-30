@extends('layouts.app')
@section('title', __('ui.products.title') . ' — Sazara Global')
@section('content')
<section class="section section-alt">
    <div class="container">
        <div class="section-head"><h2>{{ __('ui.products.title') }}</h2><div class="bar"></div><p>{{ __('ui.products.sub') }}</p></div>
        <div class="grid-3">
            @foreach ($products as $product)
                <article class="card">
                    <div class="card-img" style="background-image:url('{{ $product->imageUrl() }}')"></div>
                    <div class="card-body">
                        <h3>{{ $product->tr('name') }}</h3>
                        <p>{{ $product->tr('description') }}</p>
                        <div class="card-actions">
                            <a class="btn btn-outline" href="{{ route('products.show', $product->slug) }}">{{ __('ui.common.learn_more') }}</a>
                            <a class="btn btn-secondary" data-wa="{{ $product->tr('name') }}" href="#">{{ __('ui.common.get_offer') }}</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endsection