@extends('admin.layouts.admin')

@section('title', 'Languages & Translations')
@section('page-title', 'Languages & Translations')
@section('breadcrumb')
    <span class="adm-breadcrumb-sep">/</span>
    <span>Translations</span>
@endsection

@push('head')
<style>
@keyframes saIn {
    from { opacity: 0; transform: scale(0.96) translateY(8px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
</style>
@endpush

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

{{-- Global Sync All Card --}}
<div class="adm-card adm-mb-24" id="sync-all-card">
    <div class="adm-card-header">
        <div class="adm-card-title">🌍 Global Translation Sync</div>
        <div style="font-size:12px; color:var(--adm-muted);">Sync missing translations for all active languages at once</div>
    </div>
    <div class="adm-card-body">
        <div class="adm-flex adm-gap-16" style="flex-wrap:wrap; align-items:center;">
            {{-- Coverage grid --}}
            <div style="flex:1; min-width:200px;">
                <div style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.06em; color:var(--adm-muted); margin-bottom:10px;">Coverage</div>
                <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(175px,1fr)); gap:7px;">
                    @foreach([['🔤','UI Labels & System Messages'],['📦','Products'],['📰','Articles'],['📄','Static Pages'],['🖼️','Gallery Categories'],['🗂️','Gallery Items']] as [$ico,$lbl])
                    <div style="display:flex;align-items:center;gap:7px;font-size:13px;color:var(--adm-text);background:var(--adm-bg-alt,#f9fafb);border-radius:7px;padding:6px 10px;">
                        <span>{{ $ico }}</span><span>{{ $lbl }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            {{-- Stats + button --}}
            <div style="flex:0 0 auto; text-align:center; min-width:190px;">
                <div style="display:flex; gap:24px; justify-content:center; margin-bottom:16px;">
                    <div>
                        <div style="font-size:26px;font-weight:800;color:#2563eb;line-height:1;">{{ \App\Models\Language::where('is_active',true)->whereNotIn('code',['en','id'])->count() }}</div>
                        <div style="font-size:11px;color:var(--adm-muted);text-transform:uppercase;letter-spacing:.05em;margin-top:2px;">Languages</div>
                    </div>
                    <div style="width:1px;background:var(--adm-border,#e5e7eb);"></div>
                    <div>
                        <div style="font-size:26px;font-weight:800;color:#2563eb;line-height:1;">271</div>
                        <div style="font-size:11px;color:var(--adm-muted);text-transform:uppercase;letter-spacing:.05em;margin-top:2px;">Items/Lang</div>
                    </div>
                </div>
                <button id="btn-sync-all"
                    class="adm-btn adm-btn-primary"
                    style="width:100%;display:inline-flex;align-items:center;justify-content:center;gap:8px;font-size:14px;font-weight:600;padding:10px 20px;"
                    data-url="{{ route('admin.translations.sync-all') }}"
                    data-csrf="{{ csrf_token() }}"
                    onclick="TranslationRunner.openGlobal(this)">
                    <svg style="width:16px;height:16px;flex-shrink:0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Sync All Languages</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ===== TRANSLATION MODAL SYSTEM ===== --}}
<div id="sa-overlay" style="display:none;position:fixed;inset:0;z-index:9998;background:rgba(15,23,42,.55);backdrop-filter:blur(4px);align-items:center;justify-content:center;padding:16px;">

    {{-- CONFIRM PANEL --}}
    <div id="sa-confirm" style="background:#fff;border-radius:18px;padding:32px 28px;width:min(480px,100%);box-shadow:0 30px 70px rgba(0,0,0,.2);animation:saIn .22s ease;position:relative;">
        <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:20px;">
            <div style="width:48px;height:48px;border-radius:12px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg id="sa-confirm-icon" style="width:24px;height:24px;color:#2563eb;" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2">
                    <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div>
                <div id="sa-confirm-badge" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#2563eb;margin-bottom:3px;">
                    Auto-Translate Confirmation
                </div>
                <div id="sa-confirm-title" style="font-size:18px;font-weight:700;color:#0f172a;line-height:1.3;">
                    Translate Missing Items?
                </div>
                <div id="sa-confirm-desc" style="font-size:13px;color:#64748b;margin-top:4px;line-height:1.5;">
                    Translate missing items? Existing manual translations will be preserved.
                </div>
            </div>
        </div>

        <div id="sa-confirm-bullets" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:15px;margin-bottom:22px;font-size:13px;color:#334155;line-height:1.75;">
            {{-- Injected dynamically --}}
        </div>

        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button onclick="TranslationRunner.closeConfirm()" style="padding:9px 20px;border-radius:8px;border:1px solid #e2e8f0;background:#fff;color:#374151;font-size:14px;font-weight:500;cursor:pointer;transition:background .15s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#fff'">
                Cancel
            </button>
            <button id="sa-confirm-start-btn" onclick="TranslationRunner.start()" style="padding:9px 22px;border-radius:8px;border:none;background:#2563eb;color:#fff;font-size:14px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:7px;transition:background .15s;" onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                <span id="sa-confirm-btn-text">Start Translation</span>
            </button>
        </div>
    </div>

    {{-- PROGRESS PANEL --}}
    <div id="sa-progress" style="display:none;background:#fff;border-radius:18px;padding:36px 30px;width:min(440px,100%);box-shadow:0 30px 70px rgba(0,0,0,.2);animation:saIn .22s ease;text-align:center;">

        {{-- Circular progress ring --}}
        <div style="position:relative;width:140px;height:140px;margin:0 auto 22px;">
            <svg style="width:140px;height:140px;transform:rotate(-90deg);" viewBox="0 0 140 140">
                <circle cx="70" cy="70" r="60" fill="none" stroke="#e2e8f0" stroke-width="11"/>
                <circle id="sa-ring" cx="70" cy="70" r="60" fill="none" stroke="#2563eb"
                    stroke-width="11" stroke-linecap="round"
                    stroke-dasharray="376.99" stroke-dashoffset="376.99"
                    style="transition:stroke-dashoffset .5s cubic-bezier(.4,0,.2,1);"/>
            </svg>
            <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;pointer-events:none;">
                <span id="sa-pct" style="font-size:28px;font-weight:800;color:#0f172a;line-height:1;">0%</span>
                <span style="font-size:11px;color:#94a3b8;margin-top:2px;letter-spacing:.03em;">COMPLETE</span>
            </div>
        </div>

        {{-- Language status --}}
        <div id="sa-lang-label" style="font-size:16px;font-weight:700;color:#0f172a;margin-bottom:4px;">Initializing…</div>
        <div id="sa-items-label" style="font-size:13px;color:#64748b;margin-bottom:18px;">Preparing translation queue…</div>

        {{-- Language progress pills --}}
        <div id="sa-lang-track" style="display:flex;flex-wrap:wrap;gap:5px;justify-content:center;margin-bottom:20px;min-height:28px;"></div>

        {{-- Stats --}}
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:18px;">
            <div style="background:#f0fdf4;border-radius:10px;padding:12px 8px;">
                <div id="sa-stat-t" style="font-size:20px;font-weight:800;color:#15803d;">0</div>
                <div style="font-size:11px;color:#16a34a;margin-top:2px;">Translated</div>
            </div>
            <div style="background:#fefce8;border-radius:10px;padding:12px 8px;">
                <div id="sa-stat-s" style="font-size:20px;font-weight:800;color:#a16207;">0</div>
                <div style="font-size:11px;color:#ca8a04;margin-top:2px;">Skipped</div>
            </div>
            <div style="background:#fff1f2;border-radius:10px;padding:12px 8px;">
                <div id="sa-stat-f" style="font-size:20px;font-weight:800;color:#be123c;">0</div>
                <div style="font-size:11px;color:#e11d48;margin-top:2px;">Failed</div>
            </div>
        </div>

        {{-- Advanced log (collapsed) --}}
        <details style="text-align:left;margin-bottom:18px;">
            <summary style="font-size:12px;color:#94a3b8;cursor:pointer;user-select:none;padding:4px 0;list-style:none;display:flex;align-items:center;gap:5px;">
                <svg style="width:12px;height:12px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 9l-7 7-7-7"/></svg>
                Advanced Details
            </summary>
            <div id="sa-log" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:10px 12px;max-height:150px;overflow-y:auto;font-size:11px;font-family:monospace;line-height:1.9;margin-top:8px;color:#475569;white-space:pre-wrap;text-align:left;"></div>
        </details>

        {{-- Done actions (shown when finished) --}}
        <div id="sa-actions" style="display:none;flex-wrap:wrap;gap:10px;justify-content:center;">
            <button onclick="location.reload()" style="padding:10px 24px;border-radius:8px;border:none;background:#2563eb;color:#fff;font-size:14px;font-weight:600;cursor:pointer;">Refresh Page</button>
            <button id="sa-btn-retry" onclick="SyncAll.retry()" style="display:none;padding:10px 22px;border-radius:8px;border:1px solid #e2e8f0;background:#fff;color:#374151;font-size:14px;font-weight:500;cursor:pointer;">Retry Failed</button>
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
                        data-lang-flag="{{ $activeLang->flag }}"
                        data-url="{{ route('admin.translations.auto') }}"
                        data-csrf="{{ csrf_token() }}"
                        onclick="TranslationRunner.openSingle(this)">
                    <span>⚡</span> <span id="btn-label-auto">Auto-Translate Missing</span>
                </button>
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
                                data-lang-flag="{{ $activeLang->flag }}"
                                data-url="{{ route('admin.translations.auto') }}"
                                data-csrf="{{ csrf_token() }}"
                                onclick="TranslationRunner.openSingle(this)">
                            <span>⚡</span> Generate Translations for {{ $activeLang->name }}
                        </button>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// ─── UNIFIED TRANSLATION RUNNER (Single Auto-Translate & Global Sync) ─
const TranslationRunner = {
    mode: 'single', // 'single' | 'all'
    url: '',
    csrf: '',
    targetLang: '',
    targetLangName: '',
    targetLangFlag: '',
    isRunning: false,
    offset: 0,
    offsetData: {},
    currentLang: null,
    stats: { translated: 0, skipped: 0, failed: 0 },
    retryCount: 0,
    MAX_RETRIES: 3,
    circumference: 376.99, // 2 * PI * 60

    allLangs: [
        @foreach($languages->where('is_active', true)->whereNotIn('code', ['en', 'id']) as $l)
            { code: '{{ $l->code }}', name: '{{ addslashes($l->name) }}', flag: '{{ $l->flag }}' },
        @endforeach
    ],

    openSingle(btn) {
        if (this.isRunning) return;
        this.mode = 'single';
        this.url = btn.dataset.url;
        this.csrf = btn.dataset.csrf;
        this.targetLang = btn.dataset.lang;
        this.targetLangName = btn.dataset.langName || this.targetLang.toUpperCase();
        this.targetLangFlag = btn.dataset.langFlag || '🌐';

        // Set modern English confirmation texts
        document.getElementById('sa-confirm-badge').textContent = 'Auto-Translate Confirmation';
        document.getElementById('sa-confirm-title').textContent = 'Translate Missing Items?';
        document.getElementById('sa-confirm-desc').textContent = `Translate missing items for ${this.targetLangName}? Existing manual translations will be preserved.`;
        document.getElementById('sa-confirm-btn-text').textContent = 'Start Translation';

        document.getElementById('sa-confirm-bullets').innerHTML = `
            <div style="display:flex;gap:10px;margin-bottom:6px;">
                <span>${this.targetLangFlag}</span>
                <span>Target Language: <strong>${this.targetLangName} (${this.targetLang.toUpperCase()})</strong></span>
            </div>
            <div style="display:flex;gap:10px;margin-bottom:6px;">
                <span>📋</span>
                <span>Scope: <strong>271 items</strong> will be checked (UI, products, articles, pages, gallery)</span>
            </div>
            <div style="display:flex;gap:10px;margin-bottom:6px;">
                <span>🔒</span>
                <span>Existing manual translations will <strong>not</strong> be overwritten</span>
            </div>
            <div style="display:flex;gap:10px;">
                <span>⚡</span>
                <span>Processed safely in small batches (~5 items per request)</span>
            </div>
        `;

        this.showConfirm();
    },

    openGlobal(btn) {
        if (this.isRunning) return;
        this.mode = 'all';
        this.url = btn.dataset.url;
        this.csrf = btn.dataset.csrf;

        document.getElementById('sa-confirm-badge').textContent = 'Global Translation Sync';
        document.getElementById('sa-confirm-title').textContent = 'Sync All Translations?';
        document.getElementById('sa-confirm-desc').textContent = 'Sync missing translations for all active languages? Existing manual translations will be preserved.';
        document.getElementById('sa-confirm-btn-text').textContent = 'Start Sync';

        document.getElementById('sa-confirm-bullets').innerHTML = `
            <div style="display:flex;gap:10px;margin-bottom:6px;">
                <span>🌐</span>
                <span><strong>${this.allLangs.length} active languages</strong> will be synced (en &amp; id use static files)</span>
            </div>
            <div style="display:flex;gap:10px;margin-bottom:6px;">
                <span>📋</span>
                <span>Scope: <strong>271 items</strong> per language (UI, products, articles, pages, gallery)</span>
            </div>
            <div style="display:flex;gap:10px;margin-bottom:6px;">
                <span>🔒</span>
                <span>Existing manual translations will <strong>not</strong> be overwritten</span>
            </div>
            <div style="display:flex;gap:10px;">
                <span>⏱️</span>
                <span>Processed in background batches; progress is tracked continuously</span>
            </div>
        `;

        this.showConfirm();
    },

    showConfirm() {
        const overlay = document.getElementById('sa-overlay');
        const confirmPanel = document.getElementById('sa-confirm');
        const progressPanel = document.getElementById('sa-progress');

        if (overlay) overlay.style.display = 'flex';
        if (confirmPanel) confirmPanel.style.display = 'block';
        if (progressPanel) progressPanel.style.display = 'none';
    },

    closeConfirm() {
        if (this.isRunning) return;
        const overlay = document.getElementById('sa-overlay');
        const confirmPanel = document.getElementById('sa-confirm');
        if (confirmPanel) confirmPanel.style.display = 'none';
        if (overlay) overlay.style.display = 'none';
    },

    start() {
        if (this.isRunning) return;
        this.isRunning = true;
        this.offset = 0;
        this.offsetData = {};
        this.currentLang = null;
        this.stats = { translated: 0, skipped: 0, failed: 0 };
        this.retryCount = 0;

        const confirmPanel = document.getElementById('sa-confirm');
        const progressPanel = document.getElementById('sa-progress');
        const actions = document.getElementById('sa-actions');
        const ring = document.getElementById('sa-ring');

        if (confirmPanel) confirmPanel.style.display = 'none';
        if (progressPanel) progressPanel.style.display = 'block';
        if (actions) actions.style.display = 'none';
        if (ring) {
            ring.style.stroke = '#2563eb';
            ring.style.strokeDashoffset = this.circumference;
        }

        this.updateStatsUI();
        this.renderLangPills();

        if (this.mode === 'single') {
            this.setProgress(0, `Initializing ${this.targetLangName}…`, 'Preparing items…');
            this.appendLog(`🚀 Starting Auto-Translate for ${this.targetLangName} (${this.targetLang.toUpperCase()})...`);
            this.runSingleBatch();
        } else {
            this.setProgress(0, 'Initializing queue…', 'Preparing translation items…');
            this.appendLog(`🚀 Starting Global Translation Sync for ${this.allLangs.length} languages...`);
            this.runGlobalBatch();
        }
    },

    renderLangPills() {
        const container = document.getElementById('sa-lang-track');
        if (!container) return;
        container.innerHTML = '';

        if (this.mode === 'single') {
            const pill = document.createElement('span');
            pill.id = `sa-pill-${this.targetLang}`;
            pill.style.cssText = 'padding:4px 12px;border-radius:999px;font-size:12px;font-weight:700;border:1px solid #2563eb;color:#1d4ed8;background:#eff6ff;display:inline-flex;align-items:center;gap:5px;box-shadow:0 0 0 2px rgba(37,99,235,0.15);';
            pill.innerHTML = `<span>${this.targetLangFlag}</span><span>${this.targetLangName.toUpperCase()}</span>`;
            container.appendChild(pill);
        } else {
            this.allLangs.forEach(l => {
                const pill = document.createElement('span');
                pill.id = `sa-pill-${l.code}`;
                pill.style.cssText = 'padding:3px 8px;border-radius:999px;font-size:11px;font-weight:600;border:1px solid #e2e8f0;color:#64748b;background:#f8fafc;display:inline-flex;align-items:center;gap:3px;transition:all .2s;';
                pill.innerHTML = `<span>${l.flag || '🌐'}</span><span>${l.code.toUpperCase()}</span>`;
                container.appendChild(pill);
            });
        }
    },

    setLangPillStatus(code, status) {
        const pill = document.getElementById(`sa-pill-${code}`);
        if (!pill) return;
        if (status === 'active') {
            pill.style.borderColor = '#2563eb';
            pill.style.color = '#1d4ed8';
            pill.style.background = '#eff6ff';
            pill.style.boxShadow = '0 0 0 2px rgba(37,99,235,0.15)';
        } else if (status === 'done') {
            pill.style.borderColor = '#86efac';
            pill.style.color = '#15803d';
            pill.style.background = '#f0fdf4';
            pill.style.boxShadow = 'none';
        } else if (status === 'failed') {
            pill.style.borderColor = '#fca5a5';
            pill.style.color = '#b91c1c';
            pill.style.background = '#fef2f2';
            pill.style.boxShadow = 'none';
        }
    },

    setProgress(pct, langTitle, itemsSub) {
        if (pct !== null) {
            pct = Math.max(0, Math.min(100, Math.round(pct)));
            const pctEl = document.getElementById('sa-pct');
            const ring = document.getElementById('sa-ring');
            if (pctEl) pctEl.textContent = `${pct}%`;
            if (ring) {
                const offset = this.circumference - (pct / 100) * this.circumference;
                ring.style.strokeDashoffset = offset;
            }
        }
        const langEl = document.getElementById('sa-lang-label');
        const itemsEl = document.getElementById('sa-items-label');
        if (langEl && langTitle) langEl.textContent = langTitle;
        if (itemsEl && itemsSub) itemsEl.innerHTML = itemsSub;
    },

    updateStatsUI() {
        const st = document.getElementById('sa-stat-t');
        const ss = document.getElementById('sa-stat-s');
        const sf = document.getElementById('sa-stat-f');
        if (st) st.textContent = this.stats.translated;
        if (ss) ss.textContent = this.stats.skipped;
        if (sf) sf.textContent = this.stats.failed;
    },

    appendLog(msg) {
        const log = document.getElementById('sa-log');
        if (!log) return;
        const time = new Date().toLocaleTimeString();
        log.textContent += `[${time}] ${msg}\n`;
        log.scrollTop = log.scrollHeight;
    },

    // Single language runner
    runSingleBatch() {
        fetch(this.url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrf,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ target_lang: this.targetLang, offset: this.offset }),
        })
        .then(r => {
            if (!r.ok) throw new Error('Server responded with HTTP ' + r.status);
            return r.json();
        })
        .then(data => {
            this.retryCount = 0;
            this.stats.translated += (data.translated || 0);
            this.stats.skipped    += (data.skipped    || 0);
            this.stats.failed     += (data.failed     || 0);
            this.updateStatsUI();

            const total = data.total || 271;
            this.offset = (typeof data.offset === 'number') ? data.offset : (this.offset + 5);

            const pct = data.done ? 100 : Math.min(99, Math.round((this.offset / total) * 100));
            const langTitle = `Translating ${this.targetLangName} (${this.targetLang.toUpperCase()})`;
            const itemsSub = `${Math.min(this.offset, total)} of ${total} items processed &bull; ${pct}% completed`;

            this.setProgress(pct, langTitle, itemsSub);
            this.appendLog(`Batch: ${Math.min(this.offset, total)}/${total} (+${data.translated || 0} translated, ${data.skipped || 0} skipped, ${data.failed || 0} failed)`);

            if (data.done) {
                this.setLangPillStatus(this.targetLang, 'done');
                this.finishSuccess(`All items for ${this.targetLangName} have been processed.`);
            } else {
                setTimeout(() => this.runSingleBatch(), 250);
            }
        })
        .catch(err => {
            this.retryCount++;
            this.appendLog(`⚠ Network issue (attempt ${this.retryCount}/${this.MAX_RETRIES}): ${err.message}`);
            if (this.retryCount <= this.MAX_RETRIES) {
                this.setProgress(null, `Retrying ${this.targetLangName}…`, `Connection retry in ${this.retryCount * 2}s (attempt ${this.retryCount}/${this.MAX_RETRIES})`);
                setTimeout(() => this.runSingleBatch(), 2000 * this.retryCount);
            } else {
                this.handleFailure(err.message);
            }
        });
    },

    // Global multi-language runner
    runGlobalBatch() {
        const body = { offset_data: this.offsetData };
        if (this.currentLang) body.current_lang = this.currentLang;

        fetch(this.url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrf,
                'Accept': 'application/json',
            },
            body: JSON.stringify(body),
        })
        .then(r => {
            if (!r.ok) throw new Error('Server responded with HTTP ' + r.status);
            return r.json();
        })
        .then(data => {
            this.retryCount = 0;
            this.stats.translated += (data.translated || 0);
            this.stats.skipped    += (data.skipped    || 0);
            this.stats.failed     += (data.failed     || 0);
            this.updateStatsUI();

            this.offsetData  = data.offset_data || this.offsetData;
            const langCode   = data.lang;
            const meta       = this.allLangs.find(l => l.code === langCode) || { code: langCode, name: langCode.toUpperCase(), flag: '🌐' };
            const totalLangs = data.total_langs || this.allLangs.length || 1;
            const langIndex  = (typeof data.lang_index === 'number') ? data.lang_index : 0;
            const itemOffset = data.offset || 0;
            const itemTotal  = data.total || 271;

            this.setLangPillStatus(langCode, 'active');

            const langFraction = totalLangs > 0 ? (langIndex / totalLangs) : 0;
            const itemFraction = itemTotal > 0 ? Math.min(itemOffset / itemTotal, 1) : 0;
            const overallPct = data.done ? 100 : Math.min(99, ((langFraction + (itemFraction / totalLangs)) * 100));

            const langDisplayTitle = `Translating ${meta.name} (${meta.code.toUpperCase()})`;
            const itemsSub = `Language ${langIndex + 1} of ${totalLangs} &bull; ${Math.min(itemOffset, itemTotal)} of ${itemTotal} items processed &bull; ${Math.round(overallPct)}% completed`;

            this.setProgress(overallPct, langDisplayTitle, itemsSub);

            if (data.lang_done) {
                this.setLangPillStatus(langCode, (data.failed > 0 && data.translated === 0) ? 'failed' : 'done');
                this.appendLog(`✓ [${langCode.toUpperCase()}] Completed: ${itemTotal} items checked.`);
            }

            if (data.done) {
                this.finishSuccess(`All ${this.allLangs.length} languages successfully synchronized.`);
            } else {
                this.currentLang = data.next_lang || this.currentLang;
                setTimeout(() => this.runGlobalBatch(), 250);
            }
        })
        .catch(err => {
            this.retryCount++;
            this.appendLog(`⚠ Network issue (attempt ${this.retryCount}/${this.MAX_RETRIES}): ${err.message}`);
            if (this.retryCount <= this.MAX_RETRIES) {
                const langTitle = this.currentLang ? `Retrying…` : 'Retrying connection…';
                this.setProgress(null, langTitle, `Connection retry in ${this.retryCount * 2}s (attempt ${this.retryCount}/${this.MAX_RETRIES})`);
                setTimeout(() => this.runGlobalBatch(), 2000 * this.retryCount);
            } else {
                this.handleFailure(err.message);
            }
        });
    },

    finishSuccess(subtitle) {
        this.isRunning = false;
        this.setProgress(100, 'Translation Complete! 🎉', subtitle);

        const ring = document.getElementById('sa-ring');
        if (ring) ring.style.stroke = '#16a34a';

        this.appendLog(`\n🎉 FINISHED! ${this.stats.translated} translated, ${this.stats.skipped} skipped, ${this.stats.failed} failed.`);

        const actions = document.getElementById('sa-actions');
        const retryBtn = document.getElementById('sa-btn-retry');
        if (actions) actions.style.display = 'flex';
        if (retryBtn) retryBtn.style.display = (this.stats.failed > 0) ? 'inline-block' : 'none';
    },

    handleFailure(errorMsg) {
        this.isRunning = false;
        const ring = document.getElementById('sa-ring');
        if (ring) ring.style.stroke = '#e11d48';

        const langTitle = 'Translation Paused';
        const itemsSub = `<span style="color:#b91c1c;">Connection error: ${errorMsg}. You can resume right away.</span>`;
        this.setProgress(null, langTitle, itemsSub);

        const actions = document.getElementById('sa-actions');
        const retryBtn = document.getElementById('sa-btn-retry');
        if (actions) actions.style.display = 'flex';
        if (retryBtn) {
            retryBtn.style.display = 'inline-block';
            retryBtn.textContent = 'Resume Translation';
            retryBtn.style.background = '#2563eb';
            retryBtn.style.color = '#fff';
            retryBtn.style.borderColor = '#2563eb';
        }
    },

    retry() {
        this.retryCount = 0;
        const actions = document.getElementById('sa-actions');
        if (actions) actions.style.display = 'none';

        const ring = document.getElementById('sa-ring');
        if (ring) ring.style.stroke = '#2563eb';

        this.isRunning = true;
        this.appendLog('🔄 Resuming translation from last position...');

        if (this.mode === 'single') {
            this.runSingleBatch();
        } else {
            this.runGlobalBatch();
        }
    }
};

// Aliases for compatibility
const SyncAll = {
    confirm: (btn) => TranslationRunner.openGlobal(btn),
    start: () => TranslationRunner.start(),
    closeConfirm: () => TranslationRunner.closeConfirm(),
    retry: () => TranslationRunner.retry(),
};
function startBatchTranslate(btn) {
    TranslationRunner.openSingle(btn);
}

// Close overlay on backdrop click only if not actively running
document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.getElementById('sa-overlay');
    if (overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === this && !TranslationRunner.isRunning) {
                TranslationRunner.closeConfirm();
            }
        });
    }
});
</script>
@endpush

