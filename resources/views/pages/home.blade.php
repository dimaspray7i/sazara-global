@extends('layouts.app')
@section('title', __('ui.home.title1') . ' ' . __('ui.home.title2') . ' — Sazara Global')
@section('content')
<section class="hero">
    <div class="container">
        <p class="eyebrow">{{ __('ui.home.eyebrow') }}</p>
        <h1>{{ __('ui.home.title1') }}<br>{{ __('ui.home.title2') }}</h1>
        <p class="lead">{{ __('ui.home.lead') }}</p>
        <a class="btn btn-secondary" href="{{ route('products.index') }}">{{ __('ui.common.view_products') }}</a>
        <a class="btn btn-outline" data-wa="default" href="#">{{ __('ui.common.get_offer') }}</a>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head"><h2>{{ __('ui.home.flow_title') }}</h2><div class="bar"></div><p>{{ __('ui.home.flow_sub') }}</p></div>
        <div class="flow-grid">
            @foreach (__('ui.home.flow') as $i => $step)
                <div class="flow-card"><span class="flow-num">{{ $i + 1 }}</span><h3>{{ $step[0] }}</h3><p>{{ $step[1] }}</p></div>
            @endforeach
        </div>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <div class="section-head"><h2>{{ __('ui.home.featured_title') }}</h2><div class="bar"></div></div>
        <div class="grid-3">
            @foreach ($featured as $product)
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

<section class="section">
    <div class="container">
        <div class="section-head"><h2>{{ __('ui.home.why_title') }}</h2><div class="bar"></div></div>
        <div class="grid-4">
            @foreach (__('ui.home.why') as $item)
                <div class="flow-card"><h3>{{ $item[0] }}</h3><p>{{ $item[1] }}</p></div>
            @endforeach
        </div>
    </div>
</section>

<section class="cta-strip">
    <div class="container">
        <h2>{{ __('ui.home.cta_title') }}</h2>
        <p>{{ __('ui.home.cta_text') }}</p>
        <a class="btn btn-secondary" data-wa="default" href="#">{{ __('ui.common.chat_wa') }}</a>
        <a class="btn btn-outline light" href="{{ route('contact') }}">{{ __('ui.nav.contact') }}</a>
    </div>
</section>
@endsection