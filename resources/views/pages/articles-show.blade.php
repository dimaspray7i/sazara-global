@extends('layouts.app')
@section('title', $article->tr('title') . ' — Sazara Global')
@section('content')
<section class="section">
    <div class="container article-detail-container">
        <p class="meta back-link-wrap">
            <a class="back-link" href="{{ route('articles.index') }}">&larr; {{ __('ui.common.back_articles') }}</a>
            <span class="meta-sep">·</span>
            <span class="article-meta-date">{{ $article->published_at?->format('d M Y') }}</span>
        </p>
        <div class="section-head">
            <h2>{{ $article->tr('title') }}</h2>
            <div class="bar"></div>
        </div>
        <div class="article-detail-thumb">
            <img src="{{ $article->imageUrl() }}" alt="{{ $article->tr('title') }}" loading="lazy">
        </div>
        <div class="article-body">
            {!! nl2br(e($article->tr('body'))) !!}
        </div>
        <div class="article-detail-footer">
            <a class="btn btn-outline" href="{{ route('articles.index') }}">&larr; {{ __('ui.common.back_articles') }}</a>
            <a class="btn btn-secondary" data-wa="default" href="{{ $globalWaUrl }}" target="_blank" rel="noopener">{{ __('ui.common.chat_wa') }}</a>
        </div>
    </div>
</section>
@endsection