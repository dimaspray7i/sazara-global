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
        <div class="adm-stat-value">{{ $stats['pages'] }}</div>
        <div class="adm-stat-sub">Home, About, Products, Articles, Contact</div>
    </div>
</div>

{{-- WhatsApp Integration Quick Status --}}
<div class="adm-card adm-mb-24" style="border-left: 4px solid var(--adm-green);">
    <div class="adm-card-body" style="padding: 18px 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(60, 140, 43, 0.12); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="#3C8C2B"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
            </div>
            <div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-weight: 700; color: var(--adm-navy); font-size: 14px;">WhatsApp Integration</span>
                    <span class="adm-badge adm-badge-green">Active</span>
                </div>
                <div class="adm-text-muted adm-text-sm" style="margin-top: 2px;">
                    Target: <strong>{{ $stats['wa_display'] }}</strong> (<code>https://wa.me/{{ $stats['wa_clean'] }}</code>)
                </div>
            </div>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="https://wa.me/{{ $stats['wa_clean'] }}" target="_blank" rel="noopener" class="adm-btn adm-btn-outline adm-btn-sm">
                Test WhatsApp Link &nearr;
            </a>
            <a href="{{ route('admin.settings.index') }}" class="adm-btn adm-btn-primary adm-btn-sm">
                Manage Settings
            </a>
        </div>
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
<div class="adm-dashboard-grid-2">

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
                                <img class="adm-table-thumb" src="{{ $img }}" alt="{{ $p->name }}" width="44" height="44" loading="lazy" onerror="this.style.display='none'">
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
                <img class="adm-media-thumb" src="{{ $m->url() }}" alt="{{ $m->alt ?: $m->original_name }}" width="160" height="120" loading="lazy">
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
