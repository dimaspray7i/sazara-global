@extends('layouts.app')

@php
    use App\Models\PageSection;
    $pageTitle = PageSection::get('products', 'header', 'title', __('ui.products.title'));
    $pageSub   = PageSection::get('products', 'header', 'subtitle', __('ui.products.sub'));
@endphp

@section('title', $pageTitle . ' — Sazara Global')
@section('content')
<section class="section section-alt">
    <div class="container">
        <div class="section-head"><h2>{{ $pageTitle }}</h2><div class="bar"></div><p>{{ $pageSub }}</p></div>
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