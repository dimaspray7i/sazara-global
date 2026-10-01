@extends('admin.layouts.admin')

@section('title', 'Media Library')
@section('page-title', 'Media Library')
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
            <div class="adm-drop-zone">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                <div class="adm-drop-zone-text">Click or drag & drop image here to upload</div>
                <div class="adm-drop-zone-hint">Supports JPG, PNG, WEBP, SVG (Max 5 MB)</div>
                <input type="file" name="file" accept="image/jpeg,image/png,image/webp,image/svg+xml" required onchange="this.form.submit()">
            </div>
        </form>
    </div>
</div>

{{-- Media Library Grid --}}
<div class="adm-card">
    <div class="adm-card-header">
        <div class="adm-card-title">All Files ({{ $media->total() }})</div>
        <form method="GET" action="{{ route('admin.media.index') }}" class="adm-flex adm-gap-8">
            <input type="text" name="search" class="adm-input" placeholder="Search filename..." value="{{ request('search') }}" style="max-width: 220px; padding: 6px 10px; font-size: 13px;">
            <button type="submit" class="adm-btn adm-btn-outline adm-btn-sm">Search</button>
            @if(request('search'))
                <a href="{{ route('admin.media.index') }}" class="adm-btn adm-btn-outline adm-btn-sm">Clear</a>
            @endif
        </form>
    </div>

    <div class="adm-card-body">
        @if($media->count())
            <div class="adm-media-grid">
                @foreach($media as $m)
                    <div class="adm-media-item" style="cursor: default;">
                        <img class="adm-media-thumb" src="{{ $m->url() }}" alt="{{ $m->alt ?: $m->original_name }}" loading="lazy">
                        <div class="adm-media-meta">
                            <div class="adm-media-name" title="{{ $m->original_name }}">{{ $m->original_name }}</div>
                            <div class="adm-media-size">{{ $m->humanSize() }} · {{ $m->created_at->format('d M Y') }}</div>
                        </div>
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
