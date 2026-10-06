@extends('layouts.app')

@section('seo_title', __('ui.search.seo_title'))
@section('seo_desc',  __('ui.search.seo_desc'))

@section('content')
<section class="search-page">
    <div class="container">
        {{-- Header & Search Bar --}}
        <div class="search-page-header">
            <h1>{{ __('ui.search.title') }}</h1>
            <form action="{{ route('search', ['locale' => app()->getLocale()]) }}" method="GET" class="search-page-form">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="q" class="search-page-input" value="{{ $query }}" placeholder="{{ __('ui.search.placeholder') }}" autocomplete="off" autofocus>
                <button type="submit" class="search-page-btn">{{ __('ui.search.title') }}</button>
            </form>

            @if(filled($query))
                <div class="search-results-meta">
                    {{ __('ui.search.results_for') }} "<strong>{{ $query }}</strong>" ({{ $total ?? $results->count() }})
                </div>
            @endif
        </div>

        {{-- Results --}}
        @if(filled($query))
            @if($results->isEmpty())
                <div class="search-no-results">
                    <h2>{{ __('ui.search.no_results') }} "{{ $query }}"</h2>
                    <p>{{ __('ui.search.try_another') }}</p>
                </div>
            @else
                <div class="search-results-grid">
                    @foreach($results as $item)
                        <a href="{{ $item['url'] }}" class="search-result-card">
                            @if(!empty($item['thumbnail']))
                                <img src="{{ $item['thumbnail'] }}" alt="{{ $item['title'] }}" class="search-result-card-thumb" loading="lazy">
                            @endif
                            <div class="search-result-card-body">
                                <span class="search-result-card-badge">{{ $item['type'] }}</span>
                                <div class="search-result-card-title">{{ $item['title'] }}</div>
                                @if(!empty($item['description']))
                                    <div class="search-result-card-desc">{{ $item['description'] }}</div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        @endif
    </div>
</section>
@endsection
