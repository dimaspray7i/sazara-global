@extends('layouts.app')
@section('title', __('ui.nav.about') . ' — Sazara Global')
@section('content')
<section class="section">
    <div class="container">
        <div class="section-head"><h2>{{ __('ui.about.heading') }}</h2><div class="bar"></div></div>
        <div class="article-body">
            @foreach (__('ui.about.intro') as $p)<p>{{ $p }}</p>@endforeach
        </div>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <div class="section-head"><h2>{{ __('ui.about.story_title') }}</h2><div class="bar"></div></div>
        <div class="article-body">
            @foreach (__('ui.about.story') as $p)<p>{{ $p }}</p>@endforeach
            <div class="about-quote">
                <p class="about-ambition">{{ __('ui.about.ambition') }}</p>
                <blockquote class="about-tagline">{{ __('ui.about.tagline') }}</blockquote>
            </div>
        </div>
    </div>
</section>

<section class="section section-green">
    <div class="container">
        <div class="vision-mission-grid">
            <div class="flow-card vision-card">
                <div class="vision-pill">{{ __('ui.about.vision_title') }}</div>
                <h3>{{ __('ui.about.vision_title') }}</h3>
                <p>{{ __('ui.about.vision') }}</p>
            </div>
            <div class="flow-card mission-card">
                <div class="vision-pill">{{ __('ui.about.mission_title') }}</div>
                <h3>{{ __('ui.about.mission_title') }}</h3>
                <p>{{ __('ui.about.mission') }}</p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head"><h2>{{ __('ui.about.values_title') }}</h2><div class="bar"></div></div>
        <div class="grid-3">
            @foreach (__('ui.about.values') as $i => $v)
                <div class="flow-card"><span class="flow-num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $v[0] }}</h3><p>{{ $v[1] }}</p></div>
            @endforeach
        </div>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <div class="section-head"><h2>{{ __('ui.about.team_title') }}</h2><div class="bar"></div></div>
        <div class="grid-3">
            @foreach (__('ui.about.team') as $t)
                <div class="flow-card team-card">
                    <img class="team-photo" src="{{ asset('images/team/' . $t[3]) }}" onerror="this.src='https://placehold.co/240x240/EAF1F7/062B55?text={{ urlencode($t[0]) }}'" alt="{{ $t[0] }}">
                    <h3>{{ $t[0] }}</h3><p class="meta">{{ $t[1] }}</p><p>{{ $t[2] }}</p>
                </div>
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