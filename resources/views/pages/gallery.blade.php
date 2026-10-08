@extends('layouts.app')

@section('seo_title', __('ui.gallery.seo_title'))
@section('seo_desc',  __('ui.gallery.seo_desc'))

@section('content')
<section class="gallery-page">
    <div class="container">
        {{-- Hero --}}
        <div class="gallery-hero">
            <div class="eyebrow">{{ __('ui.gallery.title') }}</div>
            <h1>{{ __('ui.gallery.title') }}</h1>
            <p>{{ __('ui.gallery.sub') }}</p>
        </div>

        {{-- Category Filter Tabs --}}
        <div class="gallery-tabs">
            @php
                $locale = app()->getLocale();
                $tabs = [
                    'all'         => __('ui.gallery.all'),
                    'commodities' => __('ui.gallery.commodities'),
                    'insights'    => __('ui.gallery.insights'),
                    'company'     => __('ui.gallery.company'),
                ];
                $currentCategory = $category ?? 'all';
            @endphp
            @foreach($tabs as $catKey => $catLabel)
                @php
                    $isActive = ($currentCategory === $catKey) || ($catKey === 'all' && empty($currentCategory));
                    $href = $catKey === 'all'
                        ? route('gallery.index', ['locale' => $locale])
                        : route('gallery.index', ['locale' => $locale, 'category' => $catKey]);
                @endphp
                <a href="{{ $href }}" class="gallery-tab{{ $isActive ? ' active' : '' }}">
                    {{ $catLabel }}
                </a>
            @endforeach
        </div>

        {{-- Gallery Grid --}}
        @if($galleryItems->isEmpty())
            <div class="gallery-empty">
                <div class="gallery-empty-icon" style="margin-bottom: 12px; opacity: 0.4;">
                    <svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
                    </svg>
                </div>
                <p>{{ __('ui.gallery.empty') }}</p>
            </div>
        @else
            <div class="gallery-grid">
                @foreach($galleryItems as $item)
                    @php
                        $displayTitle   = $item->tr('title');
                        $displayCaption = $item->tr('caption');
                    @endphp
                    <div class="gallery-card"
                         data-src="{{ $item->url() }}"
                         data-title="{{ $displayTitle }}"
                         data-caption="{{ $displayCaption }}"
                         role="button"
                         tabindex="0"
                         aria-label="{{ $displayTitle ?: __('ui.gallery.title') }}">
                        <div class="gallery-card-media">
                            <img src="{{ $item->url() }}"
                                 alt="{{ $displayTitle ?: 'Sazara Global' }}"
                                 loading="lazy"
                                 decoding="async"
                                 width="480" height="360">
                        </div>
                        <div class="gallery-card-overlay">
                            @if(filled($displayTitle))
                                <div class="gallery-card-info">
                                    <div class="gallery-card-title">{{ $displayTitle }}</div>
                                    @if(filled($displayCaption))
                                        <div class="gallery-card-caption">{{ $displayCaption }}</div>
                                    @endif
                                </div>
                            @endif
                            <div class="gallery-card-expand" aria-hidden="true" title="View Full Image">
                                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($galleryItems->hasPages())
                <div class="pagination-wrap" style="text-align:center; margin-top:40px;">
                    {{ $galleryItems->appends(request()->query())->links() }}
                </div>
            @endif
        @endif
    </div>
</section>

{{-- Lightbox --}}
<div class="lightbox-overlay" id="lightboxOverlay" hidden aria-modal="true" role="dialog" aria-label="{{ __('ui.gallery.title') }}">
    <button class="lightbox-close" id="lightboxClose" aria-label="{{ __('ui.gallery.lightbox_close') }}">&times;</button>
    <button class="lightbox-prev"  id="lightboxPrev"  aria-label="{{ __('ui.gallery.prev') }}">&#8249;</button>
    <button class="lightbox-next"  id="lightboxNext"  aria-label="{{ __('ui.gallery.next') }}">&#8250;</button>
    <div class="lightbox-content">
        <img class="lightbox-img" id="lightboxImg" src="" alt="">
        <div class="lightbox-title"   id="lightboxTitle"></div>
        <div class="lightbox-caption" id="lightboxCaption"></div>
    </div>
    <div class="lightbox-counter" id="lightboxCounter"></div>
</div>
@endsection
