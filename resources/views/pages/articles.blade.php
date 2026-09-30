@extends('layouts.app')
@section('title', __('ui.articles.title') . ' — Sazara Global')
@section('content')
<section class="section">
    <div class="container">
        <div class="section-head"><h2>{{ __('ui.articles.title') }}</h2><div class="bar"></div><p>{{ __('ui.articles.sub') }}</p></div>
        <div class="grid-3">
            @foreach ($articles as $article)
                <article class="card article-card">
                    <div class="card-img" style="background-image:url('{{ $article->imageUrl() }}')"></div>
                    <div class="card-body">
                        <div class="article-meta-row">
                            <span class="article-badge">{{ __('ui.nav.article') }}</span>
                            <span class="meta article-date">{{ $article->published_at?->format('d M Y') }}</span>
                        </div>
                        <h3>{{ $article->tr('title') }}</h3>
                        <p>{{ $article->tr('excerpt') }}</p>
                        <div class="card-actions">
                            <a class="btn btn-outline" href="{{ route('articles.show', $article->slug) }}">{{ __('ui.common.read_more') }}</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endsection