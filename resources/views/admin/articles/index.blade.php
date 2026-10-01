@extends('admin.layouts.admin')

@section('title', 'Articles')
@section('page-title', 'Manage Articles')
@section('breadcrumb')
    <span class="adm-breadcrumb-sep">/</span>
    <span>Articles</span>
@endsection

@section('content')
<div class="adm-card">
    <div class="adm-card-header">
        <div class="adm-card-title">All Articles ({{ $articles->count() }})</div>
        <a class="adm-btn adm-btn-green" href="{{ route('admin.articles.create') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Write New Article
        </a>
    </div>

    <div class="adm-table-wrap">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Published Date</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($articles as $a)
                <tr>
                    <td>
                        <div class="adm-row-info">
                            <img class="adm-table-thumb" src="{{ $a->imageUrl() }}" alt="{{ $a->title }}" onerror="this.src='{{ asset('images/commodities/other-commodities.jpg') }}'">
                            <div>
                                <div class="adm-row-name">{{ $a->title }}</div>
                                <div class="adm-row-sub">/articles/{{ $a->slug }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        {{ $a->published_at ? $a->published_at->format('d M Y') : '—' }}
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.articles.toggle', $a) }}" style="display:inline">
                            @csrf
                            <button type="submit" class="adm-badge {{ $a->status === 'published' ? 'adm-badge-green' : 'adm-badge-gray' }}" style="cursor:pointer;border:none">
                                {{ ucfirst($a->status) }}
                            </button>
                        </form>
                    </td>
                    <td style="text-align: right;">
                        <div class="adm-flex adm-gap-8" style="justify-content: flex-end;">
                            <a class="adm-btn-icon" href="{{ route('articles.show', $a->slug) }}" target="_blank" title="View on site">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                            </a>
                            <a class="adm-btn-icon" href="{{ route('admin.articles.edit', $a) }}" title="Edit article">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('admin.articles.destroy', $a) }}" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="adm-btn-icon danger" title="Delete article" data-confirm-delete="Delete article '{{ $a->title }}'?">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="adm-empty">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
                        <div class="adm-empty-title">No articles found</div>
                        <p>Write an article to share commodity market insights.</p>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
