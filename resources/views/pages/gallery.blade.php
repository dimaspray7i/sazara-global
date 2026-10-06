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
                <p>{{ __('ui.gallery.empty') }}</p>
            </div>
        @else
            <div class="gallery-grid">
                @foreach($galleryItems as $item)
                    <div class="gallery-card"
                         data-src="{{ $item->url() }}"
                         data-title="{{ $item->tr('title') }}"
                         data-caption="{{ $item->tr('caption') }}"
                         role="button"
                         tabindex="0"
                         aria-label="{{ $item->tr('title') }}">
                        <img src="{{ $item->url() }}"
                             alt="{{ $item->tr('title') ?: $item->original_name }}"
                             loading="lazy"
                             width="400" height="300">
                        <div class="gallery-card-overlay">
                            <div class="gallery-card-info">
                                @if($item->tr('title'))
                                    <div class="gallery-card-title">{{ $item->tr('title') }}</div>
                                @endif
                                @if($item->tr('caption'))
                                    <div class="gallery-card-caption">{{ $item->tr('caption') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="gallery-card-expand" aria-hidden="true">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>
                            </svg>
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
