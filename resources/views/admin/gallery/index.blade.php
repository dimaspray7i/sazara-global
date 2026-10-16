@extends('admin.layouts.admin')

@section('title', 'Gallery Management')
@section('page-title', 'Gallery')
@section('breadcrumb')
    <span class="adm-breadcrumb-sep">/</span>
    <span>Gallery</span>
@endsection

@section('content')

{{-- Flash messages --}}
@if(session('success'))
    <div class="adm-alert adm-alert-success adm-mb-16">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="adm-alert adm-alert-danger adm-mb-16">{{ session('error') }}</div>
@endif
@if($errors->any())
    <div class="adm-alert adm-alert-danger adm-mb-16">
        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
    </div>
@endif

{{-- Header toolbar --}}
<div class="adm-flex adm-mb-16" style="justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
    <div>
        <a href="{{ route('admin.gallery.create') }}" class="adm-btn adm-btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Gallery
        </a>
        <a href="{{ route('admin.gallery.categories') }}" class="adm-btn adm-btn-outline" style="margin-left: 8px;">
            Manage Categories
        </a>
    </div>
    <form method="GET" class="adm-flex adm-gap-8" style="flex-wrap: wrap;">
        <input type="text" name="search" value="{{ request('search') }}" class="adm-input" placeholder="Search title..." style="max-width: 180px; padding: 6px 10px; font-size: 13px;">
        <select name="category" class="adm-select" style="padding: 6px 10px; font-size: 13px;">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <select name="status" class="adm-select" style="padding: 6px 10px; font-size: 13px;">
            <option value="">All Status</option>
            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
        </select>
        <select name="type" class="adm-select" style="padding: 6px 10px; font-size: 13px;">
            <option value="">All Types</option>
            <option value="single" {{ request('type') === 'single' ? 'selected' : '' }}>Single</option>
            <option value="group" {{ request('type') === 'group' ? 'selected' : '' }}>Group</option>
        </select>
        <button type="submit" class="adm-btn adm-btn-outline adm-btn-sm">Filter</button>
        @if(request('search') || request('category') || request('status') || request('type'))
            <a href="{{ route('admin.gallery.index') }}" class="adm-btn adm-btn-outline adm-btn-sm">Clear</a>
        @endif
    </form>
</div>

{{-- Gallery Grid --}}
<div class="adm-card">
    <div class="adm-card-header">
        <div class="adm-card-title">All Galleries ({{ $galleries->total() }})</div>
    </div>
    <div class="adm-card-body">
        @if($galleries->count())
            <div class="adm-media-grid" style="grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px;">
                @foreach($galleries as $gallery)
                    <div class="adm-media-item" style="position: relative;">
                        {{-- Cover thumb --}}
                        <div class="adm-media-thumb-wrap" style="aspect-ratio: 4/3; overflow: hidden; background: #f0f0f0; border-radius: 8px 8px 0 0; position: relative;">
                            @if($gallery->coverMedia)
                                @if(str_starts_with($gallery->coverMedia->mime_type, 'video/'))
                                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#222;color:#fff;font-size:13px;">
                                        <svg viewBox="0 0 24 24" fill="white" width="32" height="32"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                    </div>
                                @else
                                    <img src="{{ $gallery->coverMedia->url() }}" alt="{{ $gallery->title }}"
                                         style="width:100%;height:100%;object-fit:cover;" loading="lazy">
                                @endif
                            @else
                                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#e8e8e8;color:#999;font-size:12px;">No Cover</div>
                            @endif

                            {{-- Badges --}}
                            <div style="position:absolute;top:6px;right:6px;display:flex;flex-direction:column;gap:4px;align-items:flex-end;">
                                <span class="adm-badge {{ $gallery->status === 'published' ? 'adm-badge-success' : 'adm-badge-muted' }}" style="font-size:10px;padding:2px 7px;">
                                    {{ $gallery->status === 'published' ? 'Published' : 'Draft' }}
                                </span>
                                <span class="adm-badge adm-badge-info" style="font-size:10px;padding:2px 7px;">
                                    {{ ucfirst($gallery->type) }}
                                </span>
                            </div>
                            {{-- Media count badge --}}
                            @if($gallery->type === 'group')
                                <div style="position:absolute;bottom:6px;left:6px;">
                                    <span class="adm-badge" style="background:rgba(0,0,0,0.6);color:#fff;font-size:10px;padding:2px 7px;">
                                        {{ $gallery->media_count }} photos/videos
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="adm-media-meta" style="padding: 10px;">
                            <div class="adm-media-name" style="font-weight:600;font-size:13px;margin-bottom:4px;" title="{{ $gallery->title }}">
                                {{ Str::limit($gallery->title, 40) }}
                            </div>
                            @if($gallery->category_slug)
                                <span class="adm-badge adm-badge-info" style="font-size:10px;padding:1px 6px;">{{ $gallery->category?->name ?? $gallery->category_slug }}</span>
                            @endif
                        </div>

                        <div class="adm-media-actions" style="padding: 0 10px 10px; display: flex; gap: 6px; flex-wrap: wrap;">
                            <a href="{{ route('admin.gallery.edit', $gallery) }}" class="adm-btn adm-btn-outline adm-btn-sm" style="font-size:11px;padding:3px 8px;">Edit</a>

                            <form method="POST" action="{{ route('admin.gallery.toggle', $gallery) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="adm-btn adm-btn-sm {{ $gallery->status === 'published' ? 'adm-btn-outline' : 'adm-btn-primary' }}" style="font-size:11px;padding:3px 8px;" title="{{ $gallery->status === 'published' ? 'Unpublish' : 'Publish' }}">
                                    {{ $gallery->status === 'published' ? 'Unpublish' : 'Publish' }}
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.gallery.destroy', $gallery) }}" style="display:inline;" onsubmit="return confirm('Delete gallery \'{{ addslashes($gallery->title) }}\'? Media files will NOT be deleted.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="adm-btn-icon danger" title="Delete gallery">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="adm-pagination" style="margin-top: 24px;">
                {{ $galleries->appends(request()->query())->links() }}
            </div>
        @else
            <div class="adm-empty">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="48" height="48"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                <div class="adm-empty-title">No galleries yet</div>
                <p>Create your first gallery to start publishing photos and videos.</p>
                <a href="{{ route('admin.gallery.create') }}" class="adm-btn adm-btn-primary" style="margin-top: 12px;">Create Gallery</a>
            </div>
        @endif
    </div>
</div>

@endsection
