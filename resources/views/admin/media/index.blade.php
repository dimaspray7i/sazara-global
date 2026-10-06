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
                    <label class="adm-label">Category</label>
                    <select name="category" class="adm-select">
                        <option value="commodities">Commodities</option>
                        <option value="insights">Insights / Operations</option>
                        <option value="company">Company</option>
                        <option value="system">System</option>
                    </select>
                </div>
                <div class="adm-form-group" style="display: flex; align-items: center; gap: 8px; margin-top: 24px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="is_public" value="1" checked>
                        <span class="adm-label" style="margin: 0;">Show in Public Gallery</span>
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
            <form method="GET" action="{{ route('admin.media.index') }}" class="adm-flex adm-gap-8">
                <select name="category" class="adm-select" onchange="this.form.submit()" style="padding: 6px 10px; font-size: 13px;">
                    <option value="">All Categories</option>
                    <option value="commodities" {{ request('category') === 'commodities' ? 'selected' : '' }}>Commodities</option>
                    <option value="insights" {{ request('category') === 'insights' ? 'selected' : '' }}>Insights</option>
                    <option value="company" {{ request('category') === 'company' ? 'selected' : '' }}>Company</option>
                    <option value="system" {{ request('category') === 'system' ? 'selected' : '' }}>System</option>
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
                        <img class="adm-media-thumb" src="{{ $m->url() }}" alt="{{ $m->alt ?: $m->original_name }}" width="200" height="150" loading="lazy">

                        {{-- Public Badge & Toggle --}}
                        <div style="position: absolute; top: 8px; right: 8px; z-index: 2;">
                            <form method="POST" action="{{ route('admin.media.toggle', $m) }}">
                                @csrf
                                <button type="submit" class="adm-badge {{ $m->is_public ? 'adm-badge-success' : 'adm-badge-danger' }}" style="cursor: pointer; border: none;" title="Click to toggle Public Gallery visibility">
                                    {{ $m->is_public ? 'Public' : 'Hidden' }}
                                </button>
                            </form>
                        </div>

                        <div class="adm-media-meta">
                            <div class="adm-media-name" title="{{ $m->title ?: $m->original_name }}">
                                <strong>{{ $m->title ?: $m->original_name }}</strong>
                            </div>
                            <div class="adm-media-size" style="font-size: 11px; margin-top: 2px;">
                                <span class="adm-badge adm-badge-info" style="font-size: 10px; padding: 1px 5px;">{{ ucfirst($m->category ?? 'commodities') }}</span>
                                {{ $m->humanSize() }}
                            </div>
                        </div>

                        {{-- Edit Metadata Accordion/Form --}}
                        <details style="margin: 8px 12px; font-size: 12px; background: rgba(0,0,0,0.03); padding: 6px; border-radius: 6px;">
                            <summary style="cursor: pointer; color: var(--brand-green); font-weight: 600;">Edit Gallery Details</summary>
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
                                <div style="margin-bottom: 6px; display: flex; gap: 4px;">
                                    <select name="category" class="adm-select" style="font-size: 11px; padding: 4px 6px; flex: 1;">
                                        <option value="commodities" {{ $m->category === 'commodities' ? 'selected' : '' }}>Commodities</option>
                                        <option value="insights" {{ $m->category === 'insights' ? 'selected' : '' }}>Insights</option>
                                        <option value="company" {{ $m->category === 'company' ? 'selected' : '' }}>Company</option>
                                        <option value="system" {{ $m->category === 'system' ? 'selected' : '' }}>System</option>
                                    </select>
                                    <button type="submit" class="adm-btn adm-btn-primary adm-btn-sm" style="font-size: 11px; padding: 4px 8px;">Save</button>
                                </div>
                            </form>
                        </details>

                        <div class="adm-media-actions">
                            <a href="{{ $m->url() }}" target="_blank" class="adm-btn-icon" title="View Full Image">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                            </a>
                            <form method="POST" action="{{ route('admin.media.destroy', $m) }}" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="adm-btn-icon danger" title="Delete file" data-confirm-delete="Delete file '{{ $m->original_name }}'?">
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
                <p>Upload your first image above to build your media library.</p>
            </div>
        @endif
    </div>
</div>
@endsection
