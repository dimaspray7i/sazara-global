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
        <div class="adm-flex adm-gap-8" style="align-items: center; flex-wrap: wrap;">
            @if($activeLang && !in_array($activeLang->code, ['en', 'id']))
                <button id="btn-auto-translate"
                        class="adm-btn adm-btn-outline adm-btn-sm"
                        style="font-size: 12px; display: inline-flex; align-items: center; gap: 5px;"
                        data-lang="{{ $activeLang->code }}"
                        data-lang-name="{{ $activeLang->name }}"
                        data-url="{{ route('admin.translations.auto') }}"
                        data-csrf="{{ csrf_token() }}"
                        onclick="startBatchTranslate(this)">
                    <span>⚡</span> <span id="btn-label-auto">Auto-Translate Missing</span>
                </button>
                <span id="translate-progress" style="font-size: 12px; color: var(--adm-muted); display:none;"></span>
            @endif
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
                <p>When users browse the website in this language, translations will fall back safely to English. You can also populate translations automatically right now:</p>
                @if($activeLang && !in_array($activeLang->code, ['en', 'id']))
                    <div style="margin-top: 14px;">
                        <button class="adm-btn adm-btn-primary adm-btn-sm"
                                data-lang="{{ $activeLang->code }}"
                                data-lang-name="{{ $activeLang->name }}"
                                data-url="{{ route('admin.translations.auto') }}"
                                data-csrf="{{ csrf_token() }}"
                                onclick="startBatchTranslate(this)">
                            <span>⚡</span> Generate Translations for {{ $activeLang->name }}
                        </button>
                        <span id="translate-progress-empty" style="display:block; margin-top: 8px; font-size: 13px; color: var(--adm-muted);"></span>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function startBatchTranslate(btn) {
    const lang     = btn.dataset.lang;
    const langName = btn.dataset.langName;
    const url      = btn.dataset.url;
    const csrf     = btn.dataset.csrf;
    const label    = document.getElementById('btn-label-auto');
    const prog     = document.getElementById('translate-progress') || document.getElementById('translate-progress-empty');

    if (! confirm('Auto-translate all missing items for ' + langName + '?\n\nRuns in small batches (~5 items each). Existing manual translations will NOT be overwritten.')) {
        return;
    }

    btn.disabled = true;
    if (label) label.textContent = 'Translating…';
    if (prog)  { prog.style.display = 'inline'; prog.textContent = 'Starting…'; }

    let offset      = 0;
    let totTranslated = 0;
    let totSkipped    = 0;
    let totFailed     = 0;
    let retryCount    = 0;
    const MAX_RETRIES = 2;

    function updateProgress(current, total) {
        if (!prog) return;
        const pct = total > 0 ? Math.min(100, Math.round((current / total) * 100)) : 0;
        prog.textContent = pct + '% (' + current + '/' + (total || '?') + ') — '
            + totTranslated + ' translated'
            + (totSkipped > 0 ? ', ' + totSkipped + ' skipped' : '')
            + (totFailed  > 0 ? ', ' + totFailed  + ' failed'  : '');
    }

    function runBatch() {
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ target_lang: lang, offset: offset }),
        })
        .then(r => {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(data => {
            retryCount = 0; // reset on success
            totTranslated += (data.translated || 0);
            totSkipped    += (data.skipped    || 0);
            totFailed     += (data.failed     || 0);
            // Server always returns next offset; fallback to +5 if missing
            offset = (typeof data.offset === 'number') ? data.offset : (offset + 5);

            updateProgress(offset, data.total || 0);

            if (data.done) {
                if (label) label.textContent = '✓ Done';
                if (prog)  prog.textContent = '✓ Done — ' + totTranslated + ' translated, '
                    + totSkipped + ' skipped, ' + totFailed + ' failed. Reloading…';
                btn.disabled = false;
                setTimeout(() => location.reload(), 2500);
            } else {
                setTimeout(runBatch, 300);
            }
        })
        .catch(err => {
            retryCount++;
            if (retryCount <= MAX_RETRIES) {
                if (prog) prog.textContent = 'Retrying batch at ' + offset + '… (attempt ' + retryCount + ')';
                setTimeout(runBatch, 2000 * retryCount);
            } else {
                if (prog) prog.textContent = '⚠ Error at offset ' + offset + ': ' + err.message
                    + ' — Click ⚡ Retry to continue from here.';
                btn.disabled = false;
                btn.dataset.offset = offset; // save resume point
                if (label) label.textContent = '⚡ Retry';
                btn.onclick = function() { offset = parseInt(btn.dataset.offset || 0); retryCount = 0; startResume(btn); };
            }
        });
    }

    function startResume(b) {
        b.disabled = true;
        if (label) label.textContent = 'Translating…';
        retryCount = 0;
        runBatch();
    }

    runBatch();
}
</script>
@endpush

