@extends('admin.layouts.admin')

@php
    $isEdit = $article->exists;
@endphp

@section('title', $isEdit ? 'Edit Article: ' . $article->title : 'Write New Article')
@section('page-title', $isEdit ? 'Edit Article' : 'Write New Article')
@section('breadcrumb')
    <span class="adm-breadcrumb-sep">/</span>
    <a href="{{ route('admin.articles.index') }}">Articles</a>
    <span class="adm-breadcrumb-sep">/</span>
    <span>{{ $isEdit ? Str::limit($article->title, 20) : 'New' }}</span>
@endsection

@section('content')
<div class="adm-card" style="max-width: 900px; margin: 0 auto;">
    <div class="adm-card-header">
        <div class="adm-card-title">{{ $isEdit ? 'Edit Article' : 'Write New Article' }}</div>
        <a class="adm-btn adm-btn-outline adm-btn-sm" href="{{ route('admin.articles.index') }}">Back to List</a>
    </div>

    <div class="adm-card-body">
        <form method="POST" action="{{ $isEdit ? route('admin.articles.update', $article) : route('admin.articles.store') }}" enctype="multipart/form-data" data-single-submit>
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="adm-form-grid">
                {{-- Titles (EN & ID) --}}
                <div class="adm-form-grid-2">
                    <div class="adm-field">
                        <label class="adm-label" for="fieldName">Title (EN) <span class="req">*</span></label>
                        <input class="adm-input" type="text" id="fieldName" name="title" value="{{ old('title', $article->title) }}" required placeholder="e.g. Why Indonesian Palm Broom Is Gaining Global Demand">
                    </div>
                    <div class="adm-field">
                        <label class="adm-label" for="title_id">Title (ID - Optional)</label>
                        <input class="adm-input" type="text" id="title_id" name="title_id" value="{{ old('title_id', $article->title_id) }}" placeholder="Judul artikel dalam Bahasa Indonesia...">
                    </div>
                </div>

                {{-- Slug --}}
                <div class="adm-field">
                    <label class="adm-label" for="fieldSlug">Slug (URL identifier)</label>
                    <input class="adm-input" type="text" id="fieldSlug" name="slug" value="{{ old('slug', $article->slug) }}" placeholder="auto-generated-from-title">
                    <span class="adm-hint">Public URL: {{ url('/articles') }}/<strong>your-slug</strong></span>
                </div>

                {{-- Excerpts --}}
                <div class="adm-form-grid-2">
                    <div class="adm-field">
                        <label class="adm-label" for="excerpt">Excerpt / Summary (EN)</label>
                        <textarea class="adm-textarea" id="excerpt" name="excerpt" placeholder="Short summary displayed on list view...">{{ old('excerpt', $article->excerpt) }}</textarea>
                    </div>
                    <div class="adm-field">
                        <label class="adm-label" for="excerpt_id">Excerpt / Summary (ID - Optional)</label>
                        <textarea class="adm-textarea" id="excerpt_id" name="excerpt_id" placeholder="Ringkasan singkat dalam Bahasa Indonesia...">{{ old('excerpt_id', $article->excerpt_id) }}</textarea>
                    </div>
                </div>

                {{-- Content Body --}}
                <div class="adm-field full">
                    <label class="adm-label" for="body">Article Body Content (EN) <span class="req">*</span></label>
                    <textarea class="adm-textarea tall" id="body" name="body" required placeholder="Write the full article content here...">{{ old('body', $article->body) }}</textarea>
                </div>

                <div class="adm-field full">
                    <label class="adm-label" for="body_id">Article Body Content (ID - Optional)</label>
                    <textarea class="adm-textarea tall" id="body_id" name="body_id" placeholder="Konten artikel lengkap dalam Bahasa Indonesia...">{{ old('body_id', $article->body_id) }}</textarea>
                </div>

                {{-- Cover Image --}}
                @include('admin.partials.image-field', [
                    'label'         => 'Cover Image',
                    'groupName'     => 'article_thumbnail',
                    'mediaPathName' => 'media_path',
                    'currentUrl'    => $isEdit ? $article->imageUrl() : null,
                    'required'      => false,
                ])

                {{-- Status & Publish Date --}}
                <div class="adm-form-grid-2">
                    <div class="adm-field">
                        <label class="adm-label" for="status">Publication Status <span class="req">*</span></label>
                        <select class="adm-select" id="status" name="status" required>
                            <option value="published" {{ old('status', $article->status ?? 'published') === 'published' ? 'selected' : '' }}>Published (Visible to public)</option>
                            <option value="draft" {{ old('status', $article->status) === 'draft' ? 'selected' : '' }}>Draft (Private)</option>
                        </select>
                    </div>
                    <div class="adm-field">
                        <label class="adm-label" for="published_at">Publish Date</label>
                        <input class="adm-input" type="date" id="published_at" name="published_at" value="{{ old('published_at', $article->published_at ? $article->published_at->format('Y-m-d') : date('Y-m-d')) }}">
                    </div>
                </div>
            </div>

            <div class="adm-divider"></div>

            <div class="adm-flex adm-gap-12" style="justify-content: flex-end;">
                <a class="adm-btn adm-btn-outline" href="{{ route('admin.articles.index') }}">Cancel</a>
                <button type="submit" class="adm-btn adm-btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ $isEdit ? 'Save Changes' : 'Publish Article' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('modals')
    @include('admin.partials.media-picker-modal', ['allMedia' => $media])
@endpush
