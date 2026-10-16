@extends('layouts.app')

@section('seo_title', __('ui.gallery.seo_title'))
@section('seo_desc',  __('ui.gallery.seo_desc'))

@section('content')
<section class="gallery-page">
    <div class="container">

        {{-- Hero --}}
        <div class="gallery-hero">
            <div class="eyebrow">{{ __('ui.gallery.title') }}</div>
            <h1>{{ $title }}</h1>
            <p>{{ $sub }}</p>
        </div>

        {{-- Category Filter Tabs (dynamic from DB) --}}
        <div class="gallery-tabs">
            @php
                $locale = app()->getLocale();
                $currentCategory = $categorySlug ?? 'all';
            @endphp

            {{-- "All" tab --}}
            <a href="{{ route('gallery.index', ['locale' => $locale]) }}"
               class="gallery-tab{{ empty($currentCategory) || $currentCategory === 'all' ? ' active' : '' }}">
                {{ __('ui.gallery.all') }}
            </a>

            {{-- Dynamic category tabs --}}
            @foreach($categories as $cat)
                <a href="{{ route('gallery.index', ['locale' => $locale, 'category' => $cat->slug]) }}"
                   class="gallery-tab{{ $currentCategory === $cat->slug ? ' active' : '' }}">
                    {{ $cat->label() }}
                </a>
            @endforeach
        </div>

        {{-- Gallery Grid --}}
        @if($galleries->isEmpty())
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
                @foreach($galleries as $gallery)
                    @php
                        $displayTitle   = $gallery->tr('title');
                        $displayCaption = $gallery->tr('description');
                        $isGroup        = $gallery->isGroup();
                        $coverUrl       = $gallery->coverUrl();
                        $coverMime      = $gallery->coverMedia?->mime_type ?? 'image/jpeg';
                        $isVideo        = str_starts_with($coverMime, 'video/');
                        $categoryName   = $gallery->category ? $gallery->category->label() : '';

                        // Collect all media items for this gallery
                        $mediaItems = $gallery->media->map(function ($m) {
                            return [
                                'id'        => $m->id,
                                'url'       => $m->url(),
                                'mime_type' => $m->mime_type,
                                'title'     => $m->tr('title'),
                                'caption'   => $m->tr('caption'),
                                'is_video'  => str_starts_with($m->mime_type, 'video/'),
                            ];
                        });

                        if ($mediaItems->isEmpty() && $coverUrl) {
                            $mediaItems = collect([[
                                'id'        => $gallery->cover_media_id ?? 0,
                                'url'       => $coverUrl,
                                'mime_type' => $coverMime,
                                'title'     => $displayTitle,
                                'caption'   => $displayCaption,
                                'is_video'  => $isVideo,
                            ]]);
                        }

                        $mediaCount = $gallery->media_count ?: $mediaItems->count();
                    @endphp
                    <div class="gallery-card"
                         id="gallery-card-{{ $gallery->id }}"
                         data-gallery-id="{{ $gallery->id }}"
                         data-gallery-type="{{ $gallery->type }}"
                         data-endpoint="{{ route('gallery.show', ['locale' => $locale, 'gallery' => $gallery->id]) }}"
                         data-cover="{{ $coverUrl }}"
                         data-title="{{ $displayTitle }}"
                         data-caption="{{ $displayCaption }}"
                         data-category="{{ $categoryName }}"
                         data-is-group="{{ $isGroup ? '1' : '0' }}"
                         data-media='@json($mediaItems)'
                         role="button"
                         tabindex="0"
                         aria-label="{{ $displayTitle ?: __('ui.gallery.title') }}"
                         onclick="openGalleryCard(this)"
                         onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();openGalleryCard(this);}">

                        <div class="gallery-card-media">
                            @if($isVideo)
                                <div class="gallery-video-thumb">
                                    <svg viewBox="0 0 24 24" fill="white" width="36" height="36"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                </div>
                            @else
                                <img src="{{ $coverUrl }}"
                                     alt="{{ $displayTitle ?: 'Sazara Global' }}"
                                     loading="lazy"
                                     decoding="async"
                                     width="480" height="360">
                            @endif
                        </div>

                        <div class="gallery-card-overlay">
                            <div class="gallery-card-info">
                                @if(filled($displayTitle))
                                    <div class="gallery-card-title">{{ $displayTitle }}</div>
                                @endif
                                @if(filled($displayCaption))
                                    <div class="gallery-card-caption">{{ Str::limit($displayCaption, 80) }}</div>
                                @endif
                                @if($isGroup && $mediaCount > 1)
                                    <div class="gallery-card-count">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                        {{ $mediaCount }} {{ __('ui.gallery.photos') }}
                                    </div>
                                @endif
                            </div>
                            <div class="gallery-card-expand" aria-hidden="true">
                                @if($isGroup)
                                    <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                                @else
                                    <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/></svg>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($galleries->hasPages())
                <div class="pagination-wrap" style="text-align:center; margin-top:40px;">
                    {{ $galleries->appends(request()->query())->links('partials.pagination') }}
                </div>
            @endif
        @endif
    </div>
</section>

{{-- ─── Lightbox Modal ───────────────────────────────────────────── --}}
<div class="lightbox-overlay" id="lightboxOverlay" hidden aria-modal="true" role="dialog" aria-label="{{ __('ui.gallery.title') }}">
    <button class="lightbox-close" id="lightboxClose" aria-label="{{ __('ui.gallery.lightbox_close') }}">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
    <button class="lightbox-prev" id="lightboxPrev" aria-label="{{ __('ui.gallery.prev') }}">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
    </button>
    <button class="lightbox-next" id="lightboxNext" aria-label="{{ __('ui.gallery.next') }}">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
    </button>
    <div class="lightbox-content" id="lightboxContent">
        {{-- Image or Video rendered here by JS --}}
        <div id="lightboxMedia" style="display:flex;align-items:center;justify-content:center;max-height:75vh;width:100%;"></div>
        <div class="lightbox-title"   id="lightboxTitle"></div>
        <div class="lightbox-caption" id="lightboxCaption"></div>
    </div>
    <div class="lightbox-counter" id="lightboxCounter"></div>

    {{-- Thumbnail strip for groups --}}
    <div id="lightboxStrip" style="display:none;position:fixed;bottom:0;left:0;right:0;background:rgba(2,8,20,0.85);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);padding:10px 16px;overflow-x:auto;white-space:nowrap;text-align:center;z-index:11005;"></div>
</div>

<script>
(function(){
let lb = { items: [], index: 0, currentGalleryTitle: '' };

// ── Open card ──────────────────────────────────────────────────────
async function openGalleryCard(card) {
    if (!card) return;
    const galleryId   = card.dataset.galleryId;
    const isGroup     = card.dataset.isGroup === '1';
    const title       = card.dataset.title   || '';
    const caption     = card.dataset.caption || '';
    const coverUrl    = card.dataset.cover;
    lb.currentGalleryTitle = title;

    let items = [];

    // 1. Try embedded JSON from data-media (Instant, 0ms latency)
    if (card.dataset.media) {
        try {
            items = JSON.parse(card.dataset.media);
        } catch(e) {
            console.error('Failed to parse data-media:', e);
        }
    }

    // 2. Fallback to AJAX endpoint if items is empty
    if (!items || items.length === 0) {
        const endpoint = card.dataset.endpoint;
        if (endpoint) {
            try {
                const res  = await fetch(endpoint, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (res.ok) {
                    const data = await res.json();
                    items = data.media || [];
                }
            } catch(e) {
                console.error('Failed to fetch gallery media:', e);
            }
        }
    }

    // 3. Last fallback: use cover image
    if (!items || items.length === 0) {
        items = [{
            url: coverUrl,
            title: title,
            caption: caption,
            is_video: !!coverUrl.match(/\.(mp4|mov|avi|webm)$/i),
            mime_type: 'image/jpeg'
        }];
    }

    lb.items = items;
    lb.index = 0;
    showLightbox();
}
window.openGalleryCard = openGalleryCard;

// ── Lightbox show/hide ─────────────────────────────────────────────
function showLightbox() {
    const overlay = document.getElementById('lightboxOverlay');
    if (!overlay) return;
    overlay.hidden = false;
    document.body.style.overflow = 'hidden';
    renderItem();
    renderStrip();
}

function closeLightbox() {
    const overlay = document.getElementById('lightboxOverlay');
    if (!overlay) return;
    overlay.hidden = true;
    document.body.style.overflow = '';
    // Pause any playing video
    overlay.querySelectorAll('video').forEach(v => {
        try { v.pause(); v.src = ''; } catch(e){}
    });
    document.getElementById('lightboxMedia').innerHTML = '';
    const strip = document.getElementById('lightboxStrip');
    if (strip) {
        strip.innerHTML = '';
        strip.style.display = 'none';
    }
}

function renderItem() {
    if (!lb.items || lb.items.length === 0) return;
    const item    = lb.items[lb.index];
    const media   = document.getElementById('lightboxMedia');
    const counter = document.getElementById('lightboxCounter');
    const prev    = document.getElementById('lightboxPrev');
    const next    = document.getElementById('lightboxNext');

    // Stop and clear previous media
    media.querySelectorAll('video').forEach(v => {
        try { v.pause(); v.src = ''; } catch(e){}
    });
    media.innerHTML = '';

    if (item.is_video || (item.mime_type && item.mime_type.startsWith('video/'))) {
        const video = document.createElement('video');
        video.src = item.url;
        video.controls = true;
        video.autoplay = true;
        video.playsInline = true;
        video.style.cssText = 'max-width:90vw;max-height:72vh;border-radius:10px;box-shadow:0 20px 60px rgba(0,0,0,0.7);outline:none;background:#000;';
        media.appendChild(video);
    } else {
        const img = document.createElement('img');
        img.src = item.url;
        img.alt = item.title || lb.currentGalleryTitle || 'Sazara Global Gallery';
        img.className = 'lightbox-img';
        img.style.cssText = 'max-width:90vw;max-height:72vh;border-radius:10px;object-fit:contain;box-shadow:0 20px 60px rgba(0,0,0,0.7);';
        media.appendChild(img);
    }

    const titleEl = document.getElementById('lightboxTitle');
    const captionEl = document.getElementById('lightboxCaption');
    if (titleEl) {
        titleEl.textContent = item.title || lb.currentGalleryTitle || '';
    }
    if (captionEl) {
        captionEl.textContent = item.caption || '';
    }

    const total = lb.items.length;
    if (counter) {
        counter.textContent = total > 1 ? `${lb.index + 1} / ${total}` : '';
        counter.style.display = total > 1 ? 'block' : 'none';
    }
    if (prev) prev.style.display = lb.index > 0 ? '' : 'none';
    if (next) next.style.display = lb.index < total - 1 ? '' : 'none';

    // Highlight active thumbnail in strip
    document.querySelectorAll('.lb-thumb').forEach((t, i) => {
        t.style.outline = i === lb.index ? '2px solid var(--brand-green, #3C8C2B)' : 'none';
        t.style.opacity = i === lb.index ? '1' : '0.45';
        if (i === lb.index) {
            t.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
        }
    });
}

function renderStrip() {
    const strip = document.getElementById('lightboxStrip');
    if (!strip) return;
    if (!lb.items || lb.items.length <= 1) {
        strip.style.display = 'none';
        return;
    }
    strip.style.display = 'block';
    strip.innerHTML = '';

    lb.items.forEach((item, i) => {
        const thumb = document.createElement('span');
        thumb.className = 'lb-thumb';
        thumb.style.cssText = 'display:inline-block;width:60px;height:45px;border-radius:6px;overflow:hidden;cursor:pointer;margin:0 4px;background:#1a2332;vertical-align:middle;transition:opacity 0.2s, outline 0.2s;';
        thumb.style.outline = i === lb.index ? '2px solid var(--brand-green, #3C8C2B)' : 'none';
        thumb.style.opacity = i === lb.index ? '1' : '0.45';

        if (item.is_video || (item.mime_type && item.mime_type.startsWith('video/'))) {
            thumb.innerHTML = '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;background:#062B55;">▶</div>';
        } else {
            const tImg = document.createElement('img');
            tImg.src = item.url;
            tImg.alt = item.title || '';
            tImg.style.cssText = 'width:100%;height:100%;object-fit:cover;display:block;';
            tImg.loading = 'lazy';
            thumb.appendChild(tImg);
        }
        thumb.addEventListener('click', (e) => {
            e.stopPropagation();
            lb.index = i;
            renderItem();
        });
        strip.appendChild(thumb);
    });
}

// ── Event listeners on load ────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    // Lightbox Controls
    const closeBtn = document.getElementById('lightboxClose');
    if (closeBtn) closeBtn.addEventListener('click', closeLightbox);

    const prevBtn = document.getElementById('lightboxPrev');
    if (prevBtn) prevBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        if (lb.index > 0) { lb.index--; renderItem(); }
    });

    const nextBtn = document.getElementById('lightboxNext');
    if (nextBtn) nextBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        if (lb.index < lb.items.length - 1) { lb.index++; renderItem(); }
    });

    const overlay = document.getElementById('lightboxOverlay');
    if (overlay) {
        overlay.addEventListener('click', function(e) {
            // Close only if clicking outside the content/strip
            if (e.target === this) closeLightbox();
        });
    }

    const content = document.getElementById('lightboxContent');
    if (content) {
        content.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    // Keyboard support
    document.addEventListener('keydown', e => {
        const ov = document.getElementById('lightboxOverlay');
        if (!ov || ov.hidden) return;
        if (e.key === 'ArrowLeft')  { if (lb.index > 0) { lb.index--; renderItem(); } }
        if (e.key === 'ArrowRight') { if (lb.index < lb.items.length - 1) { lb.index++; renderItem(); } }
        if (e.key === 'Escape')     { closeLightbox(); }
    });

    // Touch swipe support
    let touchStartX = 0;
    if (overlay) {
        overlay.addEventListener('touchstart', e => {
            touchStartX = e.touches[0].clientX;
        }, { passive: true });

        overlay.addEventListener('touchend', e => {
            const dx = e.changedTouches[0].clientX - touchStartX;
            if (Math.abs(dx) > 50) {
                if (dx < 0 && lb.index < lb.items.length - 1) { lb.index++; renderItem(); }
                if (dx > 0 && lb.index > 0) { lb.index--; renderItem(); }
            }
        }, { passive: true });
    }

    // Auto-open if ?open=ID in URL
    const urlParams = new URLSearchParams(window.location.search);
    const openId = urlParams.get('open');
    if (openId) {
        const targetCard = document.getElementById('gallery-card-' + openId);
        if (targetCard) {
            openGalleryCard(targetCard);
        }
    }
});
})();
</script>
@endsection
