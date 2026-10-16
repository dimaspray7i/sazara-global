@extends('admin.layouts.admin')

@section('title', 'Media & Gallery Library')
@section('page-title', 'Media & Gallery Library')
@section('breadcrumb')
    <span class="adm-breadcrumb-sep">/</span>
    <span>Media</span>
@endsection

@section('content')
{{-- Upload Card --}}
<div class="adm-card adm-mb-24">
    <div class="adm-card-header">
        <div class="adm-card-title">Upload New Media</div>
    </div>
    <div class="adm-card-body">
        <form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="adm-drop-zone adm-mb-16">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                <div class="adm-drop-zone-text">Click or drag & drop image here to upload</div>
                <div class="adm-drop-zone-hint">Supports JPG, PNG, WEBP, SVG (Max 5 MB)</div>
                <input type="file" name="file" accept="image/jpeg,image/png,image/webp,image/svg+xml" required id="mediaFileInput">
            </div>

            <div class="adm-grid-2 adm-gap-16">
                <div class="adm-form-group">
                    <label class="adm-label">Title (EN)</label>
                    <input type="text" name="title" class="adm-input" placeholder="e.g. Premium Vanilla Beans">
                </div>
                <div class="adm-form-group">
                    <label class="adm-label">Judul (ID)</label>
                    <input type="text" name="title_id" class="adm-input" placeholder="Contoh: Biji Vanili Premium">
                </div>
            </div>

            <div class="adm-grid-2 adm-gap-16">
                <div class="adm-form-group">
                    <label class="adm-label">Activity Category</label>
                    <select name="category" class="adm-select">
                        <option value="logistics">Logistics & Shipping</option>
                        <option value="plantation">Plantation & Farms</option>
                        <option value="harvesting">Harvesting</option>
                        <option value="packaging">Quality & Packaging</option>
                        <option value="export">Export & Loading</option>
                        <option value="company">Company & Field Visits</option>
                        <option value="commodities">Commodities (General)</option>
                        <option value="system">System / Internal (Hidden)</option>
                    </select>
                </div>
                <div class="adm-form-group" style="display: flex; flex-direction: column; gap: 8px; margin-top: 18px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="is_public" value="1" checked>
                        <span class="adm-label" style="margin: 0;">Visible in Media Library</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="is_gallery" value="1">
                        <span class="adm-label" style="margin: 0; color: var(--adm-primary, #3C8C2B); font-weight: 600;">Publish to Gallery (Default: Draft)</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="adm-btn adm-btn-primary adm-mt-8">Upload Image</button>
        </form>
    </div>
</div>

{{-- Media Library Grid --}}
<div class="adm-card">
    <div class="adm-card-header">
        <div class="adm-card-title">All Files ({{ $media->total() }})</div>
        <div class="adm-flex adm-gap-8" style="flex-wrap: wrap;">
            {{-- Category Filter --}}
            <form method="GET" action="{{ route('admin.media.index') }}" class="adm-flex adm-gap-8" style="flex-wrap: wrap;">
                <select name="category" class="adm-select" onchange="this.form.submit()" style="padding: 6px 10px; font-size: 13px;">
                    <option value="">All Categories</option>
                    <option value="logistics" {{ request('category') === 'logistics' ? 'selected' : '' }}>Logistics & Shipping</option>
                    <option value="plantation" {{ request('category') === 'plantation' ? 'selected' : '' }}>Plantation & Farms</option>
                    <option value="harvesting" {{ request('category') === 'harvesting' ? 'selected' : '' }}>Harvesting</option>
                    <option value="packaging" {{ request('category') === 'packaging' ? 'selected' : '' }}>Quality & Packaging</option>
                    <option value="export" {{ request('category') === 'export' ? 'selected' : '' }}>Export & Loading</option>
                    <option value="company" {{ request('category') === 'company' ? 'selected' : '' }}>Company & Field</option>
                    <option value="commodities" {{ request('category') === 'commodities' ? 'selected' : '' }}>Commodities</option>
                    <option value="system" {{ request('category') === 'system' ? 'selected' : '' }}>System (Hidden)</option>
                </select>
                <input type="text" name="search" class="adm-input" placeholder="Search..." value="{{ request('search') }}" style="max-width: 180px; padding: 6px 10px; font-size: 13px;">
                <button type="submit" class="adm-btn adm-btn-outline adm-btn-sm">Filter</button>
                @if(request('search') || request('category'))
                    <a href="{{ route('admin.media.index') }}" class="adm-btn adm-btn-outline adm-btn-sm">Clear</a>
                @endif
            </form>
        </div>
    </div>

    <div class="adm-card-body">
        @if($media->count())
            <div class="adm-media-grid">
                @foreach($media as $m)
                    <div class="adm-media-item" style="cursor: default; position: relative;">
                        <div class="adm-media-thumb-wrap">
                            @if(str_starts_with($m->mime_type ?? '', 'video/'))
                                <div style="position:relative;width:100%;height:100%;min-height:120px;background:#000;border-radius:4px;overflow:hidden;cursor:pointer;" onclick="playAdminVideo('{{ $m->url() }}')">
                                    <video src="{{ $m->url() }}" style="width:100%;height:100%;object-fit:cover;display:block;" muted preload="metadata"></video>
                                    <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.35);" title="Click to play video">
                                        <svg viewBox="0 0 24 24" fill="white" width="36" height="36"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                    </div>
                                </div>
                            @else
                                <img class="adm-media-thumb" src="{{ $m->url() }}" alt="{{ $m->title ?: 'Media image' }}" width="220" height="165" loading="lazy">
                            @endif
                            <div class="adm-media-top-badge" style="display: flex; gap: 4px; flex-direction: column; align-items: flex-end;">
                                <form method="POST" action="{{ route('admin.media.toggle', $m) }}" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="field" value="is_gallery">
                                    <button type="submit" class="adm-badge {{ $m->is_gallery ? 'adm-badge-success' : 'adm-badge-muted' }}" style="cursor: pointer; border: none; font-size: 10px; padding: 3px 8px; font-weight:600;" title="Click to toggle Gallery publication: Published vs Draft">
                                        {{ $m->is_gallery ? 'Gallery: Published' : 'Gallery: Draft' }}
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="adm-media-meta">
                            <div class="adm-media-name" title="{{ $m->title ?: 'Untitled Asset' }}">
                                <strong>{{ $m->title ?: 'Untitled Asset' }}</strong>
                            </div>
                            <div class="adm-media-cat">
                                <span class="adm-badge adm-badge-info" style="font-size: 10px; padding: 1px 6px;">{{ ucfirst($m->category ?? 'logistics') }}</span>
                            </div>
                        </div>

                        {{-- Clean Edit Details Accordion --}}
                        <details class="adm-media-details">
                            <summary>Edit Details</summary>
                            <form method="POST" action="{{ route('admin.media.update', $m) }}" style="margin-top: 8px;">
                                @csrf
                                @method('PUT')
                                <div style="margin-bottom: 6px;">
                                    <input type="text" name="title" class="adm-input" value="{{ $m->title }}" placeholder="Title (EN)" style="font-size: 11px; padding: 4px 6px;">
                                </div>
                                <div style="margin-bottom: 6px;">
                                    <input type="text" name="title_id" class="adm-input" value="{{ $m->title_id }}" placeholder="Judul (ID)" style="font-size: 11px; padding: 4px 6px;">
                                </div>
                                <div style="margin-bottom: 6px;">
                                    <input type="text" name="caption" class="adm-input" value="{{ $m->caption }}" placeholder="Caption (EN)" style="font-size: 11px; padding: 4px 6px;">
                                </div>
                                <div style="margin-bottom: 6px;">
                                    <input type="text" name="caption_id" class="adm-input" value="{{ $m->caption_id }}" placeholder="Keterangan (ID)" style="font-size: 11px; padding: 4px 6px;">
                                </div>
                                <div style="margin-bottom: 6px;">
                                    <select name="category" class="adm-select" style="font-size: 11px; padding: 4px 6px; width: 100%;">
                                        <option value="logistics" {{ $m->category === 'logistics' ? 'selected' : '' }}>Logistics & Shipping</option>
                                        <option value="plantation" {{ $m->category === 'plantation' ? 'selected' : '' }}>Plantation & Farms</option>
                                        <option value="harvesting" {{ $m->category === 'harvesting' ? 'selected' : '' }}>Harvesting</option>
                                        <option value="packaging" {{ $m->category === 'packaging' ? 'selected' : '' }}>Quality & Packaging</option>
                                        <option value="export" {{ $m->category === 'export' ? 'selected' : '' }}>Export & Loading</option>
                                        <option value="company" {{ $m->category === 'company' ? 'selected' : '' }}>Company & Field</option>
                                        <option value="commodities" {{ $m->category === 'commodities' ? 'selected' : '' }}>Commodities</option>
                                        <option value="system" {{ $m->category === 'system' ? 'selected' : '' }}>System (Hidden)</option>
                                    </select>
                                </div>
                                <div style="margin-bottom: 8px; display: flex; flex-direction: column; gap: 4px; font-size: 11px;">
                                    <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                                        <input type="checkbox" name="is_public" value="1" {{ $m->is_public ? 'checked' : '' }}>
                                        <span>Visible in Media Library</span>
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                                        <input type="checkbox" name="is_gallery" value="1" {{ $m->is_gallery ? 'checked' : '' }}>
                                        <span style="color: var(--adm-primary, #3C8C2B); font-weight: 600;">Gallery Status: Published</span>
                                    </label>
                                </div>
                                <button type="submit" class="adm-btn adm-btn-primary adm-btn-sm" style="font-size: 11px; padding: 4px 8px; width: 100%;">Save Changes</button>
                            </form>
                        </details>

                        <div class="adm-media-actions">
                            <a href="{{ $m->url() }}" target="_blank" class="adm-btn-icon" title="View Full Image">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                            </a>
                            <form method="POST" action="{{ route('admin.media.destroy', $m) }}" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="adm-btn-icon danger" title="Delete file" data-confirm-delete="Delete media asset '{{ $m->title ?: 'this image' }}'?">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="adm-pagination">
                {{ $media->links() }}
            </div>
        @else
            <div class="adm-empty">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                <div class="adm-empty-title">No media files found</div>
            </div>
        @endif
    </div>
</div>

{{-- Admin HTML5 Video Player Modal --}}
<div id="adminVideoModal" style="display:none;position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,0.85);align-items:center;justify-content:center;padding:20px;" onclick="if(event.target===this)closeAdminVideo();">
    <div style="position:relative;max-width:860px;width:100%;background:#000;border-radius:12px;overflow:hidden;box-shadow:0 12px 48px rgba(0,0,0,0.8);">
        <button type="button" onclick="closeAdminVideo()" style="position:absolute;top:12px;right:12px;z-index:10;background:rgba(255,255,255,0.25);color:#fff;border:none;border-radius:50%;width:34px;height:34px;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .2s;" onmouseover="this.style.background='rgba(255,255,255,0.4)'" onmouseout="this.style.background='rgba(255,255,255,0.25)'">✕</button>
        <video id="adminModalVideoPlayer" controls playsinline style="width:100%;max-height:80vh;display:block;outline:none;background:#000;"></video>
    </div>
</div>

@push('scripts')
<script>
function playAdminVideo(url) {
    const modal = document.getElementById('adminVideoModal');
    const player = document.getElementById('adminModalVideoPlayer');
    if (!modal || !player) return;
    player.src = url;
    modal.style.display = 'flex';
    player.play().catch(e => console.log('Autoplay deferred:', e));
}

function closeAdminVideo() {
    const modal = document.getElementById('adminVideoModal');
    const player = document.getElementById('adminModalVideoPlayer');
    if (!modal || !player) return;
    try { player.pause(); player.src = ''; } catch(e){}
    modal.style.display = 'none';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeAdminVideo();
});
</script>
@endpush
@endsection
