@extends('admin.layouts.admin')

@section('title', 'Languages & Translations')
@section('page-title', 'Languages & Translations')
@section('breadcrumb')
    <span class="adm-breadcrumb-sep">/</span>
    <span>Translations</span>
@endsection

@section('content')
<div class="adm-grid-2 adm-gap-24 adm-mb-24">
    {{-- Languages Management Card --}}
    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">Available Languages ({{ $languages->count() }})</div>
        </div>
        <div class="adm-card-body" style="max-height: 380px; overflow-y: auto;">
            <table class="adm-table" style="font-size: 13px;">
                <thead>
                    <tr>
                        <th>Flag</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Dir</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($languages as $lang)
                        <tr>
                            <td style="font-size: 18px;">{{ $lang->flag }}</td>
                            <td><code>{{ $lang->code }}</code></td>
                            <td>
                                <strong>{{ $lang->native_name }}</strong><br>
                                <small style="color: var(--adm-muted);">{{ $lang->name }}</small>
                            </td>
                            <td><span class="adm-badge adm-badge-info" style="font-size: 10px;">{{ strtoupper($lang->direction) }}</span></td>
                            <td>
                                <span class="adm-badge {{ $lang->is_active ? 'adm-badge-success' : 'adm-badge-danger' }}" style="font-size: 10px;">
                                    {{ $lang->is_active ? 'Active' : 'Disabled' }}
                                </span>
                            </td>
                            <td>
                                @if($lang->code !== 'en')
                                    <form method="POST" action="{{ route('admin.languages.toggle', $lang) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="adm-btn adm-btn-outline adm-btn-sm" style="font-size: 11px; padding: 2px 6px;">
                                            {{ $lang->is_active ? 'Disable' : 'Enable' }}
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add New Language Card --}}
    <div class="adm-card">
        <div class="adm-card-header">
            <div class="adm-card-title">Add New Language</div>
        </div>
        <div class="adm-card-body">
            <form method="POST" action="{{ route('admin.languages.store') }}">
                @csrf
                <div class="adm-form-grid-2">
                    <div class="adm-field">
                        <label class="adm-label">ISO Code (e.g. 'nl', 'tr') <span class="req">*</span></label>
                        <input class="adm-input" type="text" name="code" required placeholder="e.g. tr" maxlength="10">
                    </div>
                    <div class="adm-field">
                        <label class="adm-label">Flag Emoji <span class="req">*</span></label>
                        <input class="adm-input" type="text" name="flag" placeholder="e.g. 🇹🇷">
                    </div>
                </div>

                <div class="adm-form-grid-2">
                    <div class="adm-field">
                        <label class="adm-label">English Name <span class="req">*</span></label>
                        <input class="adm-input" type="text" name="name" required placeholder="e.g. Turkish">
                    </div>
                    <div class="adm-field">
                        <label class="adm-label">Native Name <span class="req">*</span></label>
                        <input class="adm-input" type="text" name="native_name" required placeholder="e.g. Türkçe">
                    </div>
                </div>

                <div class="adm-field">
                    <label class="adm-label">Text Direction</label>
                    <select class="adm-select" name="direction">
                        <option value="ltr">Left to Right (LTR)</option>
                        <option value="rtl">Right to Left (RTL - Arabic, Hebrew, Urdu)</option>
                    </select>
                </div>

                <button type="submit" class="adm-btn adm-btn-primary adm-mt-12">Add Language</button>
            </form>
        </div>
    </div>
</div>

{{-- Translations Database & Live Editor --}}
<div class="adm-card">
    <div class="adm-card-header">
        <div class="adm-card-title">Database Content Translations</div>
        <form method="GET" action="{{ route('admin.translations.index') }}" class="adm-flex adm-gap-8">
            <label class="adm-label" style="margin: 0; align-self: center;">Select Language:</label>
            <select name="lang" class="adm-select" onchange="this.form.submit()" style="padding: 6px 12px; font-size: 13px;">
                @foreach($languages as $l)
                    <option value="{{ $l->code }}" {{ ($activeLang?->code === $l->code) ? 'selected' : '' }}>
                        {{ $l->flag }} {{ $l->name }} ({{ $l->code }})
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="adm-card-body">
        @if($translations->count())
            <table class="adm-table">
                <thead>
                    <tr>
                        <th style="width: 140px;">Type / Target</th>
                        <th style="width: 120px;">Field</th>
                        <th>Translated Content</th>
                        <th style="width: 100px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($translations as $t)
                        <tr>
                            <td>
                                <span class="adm-badge adm-badge-info" style="font-size: 11px;">{{ $t->content_type }}</span><br>
                                <small style="color: var(--adm-muted);">ID: {{ $t->content_id }}</small>
                            </td>
                            <td><code>{{ $t->field }}</code></td>
                            <td>
                                <form method="POST" action="{{ route('admin.translations.update', $t) }}" id="form-{{ $t->id }}">
                                    @csrf
                                    @method('PUT')
                                    <textarea name="value" class="adm-textarea" style="min-height: 54px; font-size: 13px; font-family: inherit;">{{ $t->value }}</textarea>
                                </form>
                            </td>
                            <td>
                                <button type="submit" form="form-{{ $t->id }}" class="adm-btn adm-btn-primary adm-btn-sm" style="font-size: 11px;">
                                    Save
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="adm-pagination">
                {{ $translations->links() }}
            </div>
        @else
            <div class="adm-empty">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                <div class="adm-empty-title">No cached translations for {{ $activeLang?->name ?? 'this language' }} yet</div>
                <p>When users browse the website in this language, translations will be automatically retrieved and cached here, ready for your customization.</p>
            </div>
        @endif
    </div>
</div>
@endsection
