@extends('admin.layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb')
    <span class="adm-breadcrumb-sep">/</span>
    <span>Dashboard</span>
@endsection

@section('content')

{{-- Stats --}}
<div class="adm-stats-grid">
    <div class="adm-stat-card green">
        <div class="adm-stat-label">Products</div>
        <div class="adm-stat-value">{{ $stats['products'] }}</div>
        <div class="adm-stat-sub">{{ $stats['active_products'] }} active</div>
    </div>
    <div class="adm-stat-card navy">
        <div class="adm-stat-label">Articles</div>
        <div class="adm-stat-value">{{ $stats['articles'] }}</div>
        <div class="adm-stat-sub">{{ $stats['published_articles'] }} published</div>
    </div>
    <div class="adm-stat-card teal">
        <div class="adm-stat-label">Media Files</div>
        <div class="adm-stat-value">{{ $stats['media'] }}</div>
        <div class="adm-stat-sub">in media library</div>
    </div>
    <div class="adm-stat-card amber">
        <div class="adm-stat-label">Pages</div>
        <div class="adm-stat-value">5</div>
        <div class="adm-stat-sub">Home, About, Products, Articles, Contact</div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="adm-card adm-mb-24">
    <div class="adm-card-header">
        <div class="adm-card-title">Quick Actions</div>
    </div>
    <div class="adm-card-body">
        <div class="adm-actions-grid">
            <a class="adm-action-btn" href="{{ route('admin.products.create') }}">
                <div class="adm-action-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </div>
                Add Product
            </a>
            <a class="adm-action-btn" href="{{ route('admin.articles.create') }}">
                <div class="adm-action-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </div>
                Add Article
            </a>
            <a class="adm-action-btn" href="{{ route('admin.media.index') }}">
                <div class="adm-action-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                </div>
                Upload Media
            </a>
            <a class="adm-action-btn" href="{{ route('admin.pages.index') }}">
                <div class="adm-action-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </div>
                Edit Pages
            </a>
            <a class="adm-action-btn" href="{{ route('admin.products.index') }}">
                <div class="adm-action-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                </div>
                Manage Products
            </a>
            <a class="adm-action-btn" href="{{ route('admin.settings.index') }}">
                <div class="adm-action-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
                </div>
                Settings
            </a>
        </div>
    </div>
</div>

{{-- Two columns: Recent Products & Articles --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

    {{-- Recent Products --}}
    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">Recent Products</div>
            <a class="adm-btn adm-btn-outline adm-btn-sm" href="{{ route('admin.products.index') }}">View All</a>
        </div>
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr><th>Product</th><th>Status</th></tr>
                </thead>
                <tbody>
                @forelse($recent_products as $p)
                    <tr>
                        <td>
                            <div class="adm-row-info">
                                @php $img = $p->imageUrl(); @endphp
                                <img class="adm-table-thumb" src="{{ $img }}" alt="{{ $p->name }}" onerror="this.style.display='none'">
                                <div>
                                    <div class="adm-row-name">{{ $p->name }}</div>
                                    <div class="adm-row-sub">{{ $p->slug }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="adm-badge {{ $p->is_active ? 'adm-badge-green' : 'adm-badge-gray' }}">
                                {{ $p->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="adm-text-muted adm-text-center" style="padding:20px">No products yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Articles --}}
    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">Recent Articles</div>
            <a class="adm-btn adm-btn-outline adm-btn-sm" href="{{ route('admin.articles.index') }}">View All</a>
        </div>
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr><th>Article</th><th>Status</th></tr>
                </thead>
                <tbody>
                @forelse($recent_articles as $a)
                    <tr>
                        <td>
                            <div class="adm-row-name">{{ Str::limit($a->title, 45) }}</div>
                            <div class="adm-row-sub">{{ $a->published_at?->format('d M Y') }}</div>
                        </td>
                        <td>
                            <span class="adm-badge {{ $a->status === 'published' ? 'adm-badge-green' : 'adm-badge-gray' }}">
                                {{ ucfirst($a->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="adm-text-muted adm-text-center" style="padding:20px">No articles yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Recent Media --}}
@if($recent_media->count())
<div class="adm-card adm-mt-24">
    <div class="adm-card-header">
        <div class="adm-card-title">Recent Media</div>
        <a class="adm-btn adm-btn-outline adm-btn-sm" href="{{ route('admin.media.index') }}">View All</a>
    </div>
    <div class="adm-card-body">
        <div class="adm-media-grid">
            @foreach($recent_media as $m)
            <div class="adm-media-item" style="cursor:default">
                <img class="adm-media-thumb" src="{{ $m->url() }}" alt="{{ $m->alt ?: $m->original_name }}" loading="lazy">
                <div class="adm-media-meta">
                    <div class="adm-media-name" title="{{ $m->original_name }}">{{ $m->original_name }}</div>
                    <div class="adm-media-size">{{ $m->humanSize() }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@endsection
