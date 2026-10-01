@extends('layouts.app')

@php
    use App\Models\PageSection;

    $heading    = PageSection::get('about', 'intro', 'heading', __('ui.about.heading'));
    $introP1    = PageSection::get('about', 'intro', 'paragraph1', __('ui.about.intro')[0] ?? '');
    $introP2    = PageSection::get('about', 'intro', 'paragraph2', __('ui.about.intro')[1] ?? '');
    $introP3    = PageSection::get('about', 'intro', 'paragraph3', __('ui.about.intro')[2] ?? '');
    $introImg   = PageSection::getMedia('about', 'intro', 'image');

    $storyTitle = PageSection::get('about', 'story', 'title', __('ui.about.story_title'));
    $storyP1    = PageSection::get('about', 'story', 'paragraph1', __('ui.about.story')[0] ?? '');
    $storyP2    = PageSection::get('about', 'story', 'paragraph2', __('ui.about.story')[1] ?? '');
    $storyP3    = PageSection::get('about', 'story', 'paragraph3', __('ui.about.story')[2] ?? '');
    $ambition   = PageSection::get('about', 'story', 'ambition', __('ui.about.ambition'));
    $tagline    = PageSection::get('about', 'story', 'tagline', __('ui.about.tagline'));
    $storyImg   = PageSection::getMedia('about', 'story', 'image');

    $visionTitle = PageSection::get('about', 'mission', 'vision_title', __('ui.about.vision_title'));
    $visionText  = PageSection::get('about', 'mission', 'vision', __('ui.about.vision'));
    $missionTitle= PageSection::get('about', 'mission', 'mission_title', __('ui.about.mission_title'));
    $missionText = PageSection::get('about', 'mission', 'mission', __('ui.about.mission'));

    $valuesTitle = PageSection::get('about', 'values', 'title', __('ui.about.values_title'));

    $ctaTitle    = PageSection::get('about', 'cta', 'title', __('ui.home.cta_title'));
    $ctaText     = PageSection::get('about', 'cta', 'description', __('ui.home.cta_text'));
    $ctaImg      = PageSection::getMedia('about', 'cta', 'image');
@endphp

@section('title', __('ui.nav.about') . ' — Sazara Global')
@section('content')
<section class="section">
    <div class="container">
        <div class="section-head"><h2>{{ $heading }}</h2><div class="bar"></div></div>
        @if($introImg)
            <div style="margin-bottom: 28px; border-radius: 10px; overflow: hidden; max-height: 400px;">
                <img src="{{ $introImg->url() }}" alt="{{ $heading }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
        @endif
        <div class="article-body">
            @if($introP1)<p>{{ $introP1 }}</p>@endif
            @if($introP2)<p>{{ $introP2 }}</p>@endif
            @if($introP3)<p>{{ $introP3 }}</p>@endif
        </div>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <div class="section-head"><h2>{{ $storyTitle }}</h2><div class="bar"></div></div>
        @if($storyImg)
            <div style="margin-bottom: 28px; border-radius: 10px; overflow: hidden; max-height: 400px;">
                <img src="{{ $storyImg->url() }}" alt="{{ $storyTitle }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
        @endif
        <div class="article-body">
            @if($storyP1)<p>{{ $storyP1 }}</p>@endif
            @if($storyP2)<p>{{ $storyP2 }}</p>@endif
            @if($storyP3)<p>{{ $storyP3 }}</p>@endif
            <div class="about-quote">
                <p class="about-ambition">{{ $ambition }}</p>
                <blockquote class="about-tagline">{{ $tagline }}</blockquote>
            </div>
        </div>
    </div>
</section>

<section class="section section-green">
    <div class="container">
        <div class="vision-mission-grid">
            <div class="flow-card vision-card">
                <div class="vision-pill">{{ $visionTitle }}</div>
                <h3>{{ $visionTitle }}</h3>
                <p>{{ $visionText }}</p>
            </div>
            <div class="flow-card mission-card">
                <div class="vision-pill">{{ $missionTitle }}</div>
                <h3>{{ $missionTitle }}</h3>
                <p>{{ $missionText }}</p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head"><h2>{{ $valuesTitle }}</h2><div class="bar"></div></div>
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

<section class="cta-strip" @if($ctaImg) style="background-image: url('{{ $ctaImg->url() }}');" @endif>
    <div class="container">
        <h2>{{ $ctaTitle }}</h2>
        <p>{{ $ctaText }}</p>
        <a class="btn btn-secondary" data-wa="default" href="#">{{ __('ui.common.chat_wa') }}</a>
        <a class="btn btn-outline light" href="{{ route('contact') }}">{{ __('ui.nav.contact') }}</a>
    </div>
</section>
@endsection