@extends('layouts.app')

@php
    use App\Models\PageSection;
    $heroEyebrow = PageSection::get('home', 'hero', 'eyebrow', __('ui.home.eyebrow'));
    $heroTitle1  = PageSection::get('home', 'hero', 'title', __('ui.home.title1'));
    $heroTitle2  = PageSection::get('home', 'hero', 'title2', __('ui.home.title2'));
    $heroLead    = PageSection::get('home', 'hero', 'description', __('ui.home.lead'));
    $heroBtnText = PageSection::get('home', 'hero', 'btn_text', __('ui.common.view_products'));
    $heroBtnUrl  = PageSection::get('home', 'hero', 'btn_url', route('products.index'));
    $heroImg     = PageSection::getMedia('home', 'hero', 'image');

    $flowTitle   = PageSection::get('home', 'flow', 'title', __('ui.home.flow_title'));
    $flowSub     = PageSection::get('home', 'flow', 'subtitle', __('ui.home.flow_sub'));

    $whyTitle    = PageSection::get('home', 'why', 'title', __('ui.home.why_title'));

    $ctaTitle    = PageSection::get('home', 'cta', 'title', __('ui.home.cta_title'));
    $ctaText     = PageSection::get('home', 'cta', 'description', __('ui.home.cta_text'));
    $ctaImg      = PageSection::getMedia('home', 'cta', 'image');
@endphp

@section('title', $heroTitle1 . ' ' . $heroTitle2 . ' — Sazara Global')
@section('content')
<section class="hero" @if($heroImg) style="background-image: url('{{ $heroImg->url() }}');" @endif>
    <div class="container">
        <p class="eyebrow">{{ $heroEyebrow }}</p>
        <h1>{{ $heroTitle1 }}<br>{{ $heroTitle2 }}</h1>
        <p class="lead">{{ $heroLead }}</p>
        <a class="btn btn-secondary" href="{{ $heroBtnUrl }}">{{ $heroBtnText }}</a>
        <a class="btn btn-outline" data-wa="default" href="{{ $globalWaUrl }}" target="_blank" rel="noopener">{{ __('ui.common.get_offer') }}</a>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head"><h2>{{ $flowTitle }}</h2><div class="bar"></div><p>{{ $flowSub }}</p></div>
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
                            <a class="btn btn-secondary" data-wa="{{ $product->tr('name') }}" href="{{ $product->waUrl() }}" target="_blank" rel="noopener">{{ __('ui.common.get_offer') }}</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head"><h2>{{ $whyTitle }}</h2><div class="bar"></div></div>
        <div class="grid-4">
            @foreach (__('ui.home.why') as $item)
                <div class="flow-card"><h3>{{ $item[0] }}</h3><p>{{ $item[1] }}</p></div>
            @endforeach
        </div>
    </div>
</section>

<section class="cta-strip" @if($ctaImg) style="background-image: url('{{ $ctaImg->url() }}');" @endif>
    <div class="container">
        <h2>{{ $ctaTitle }}</h2>
        <p>{{ $ctaText }}</p>
        <a class="btn btn-secondary" data-wa="default" href="{{ $globalWaUrl }}" target="_blank" rel="noopener">{{ __('ui.common.chat_wa') }}</a>
        <a class="btn btn-outline light" href="{{ route('contact') }}">{{ __('ui.nav.contact') }}</a>
    </div>
</section>
@endsection