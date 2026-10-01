@extends('admin.layouts.admin')

@section('title', 'Manage Pages')
@section('page-title', 'Page Content Management')
@section('breadcrumb')
    <span class="adm-breadcrumb-sep">/</span>
    <span>Pages</span>
@endsection

@section('content')
<div class="adm-card adm-mb-24">
    <div class="adm-card-header">
        <div class="adm-card-title">Select a Page to Edit</div>
    </div>
    <div class="adm-card-body">
        <p class="adm-text-muted adm-mb-16">
            Customize headers, hero banners, text copy, CTA sections, and images for each public page without altering Blade templates.
        </p>

        <div class="adm-pages-grid">
            @foreach($pages as $slug => $info)
                <a class="adm-page-card" href="{{ route('admin.pages.edit', $slug) }}">
                    <div class="adm-page-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                    </div>
                    <div>
                        <div class="adm-page-name">{{ $info['label'] }}</div>
                        <div class="adm-page-count">{{ count($info['sections']) }} section(s) available</div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
