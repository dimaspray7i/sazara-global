{{--
    Media Picker Modal
    Include once per page (in @push('modals'))
    Pass $allMedia collection
--}}
<div class="adm-modal-overlay" id="mediaPickerModal" role="dialog" aria-modal="true" aria-label="Media Library">
    <div class="adm-modal">
        <div class="adm-modal-head">
            <div class="adm-modal-title">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                Media Library — Choose Image
            </div>
            <button type="button" class="adm-modal-close" id="mediaPickerClose" aria-label="Close">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Quick upload inside picker --}}
        <div class="adm-modal-search" style="display:flex;gap:12px;align-items:center">
            <input type="text" class="adm-input" id="mediaPickerSearch" placeholder="Search by filename..." style="max-width:280px">
            <form id="mediaPickerUploadForm" method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" style="display:flex;align-items:center;gap:8px">
                @csrf
                <input type="hidden" name="redirect_back" value="1">
                <label class="adm-btn adm-btn-green adm-btn-sm" style="cursor:pointer;margin:0">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    Upload New
                    <input type="file" id="mediaPickerUploadInput" name="file" accept="image/jpeg,image/png,image/webp,image/svg+xml" style="display:none">
                </label>
            </form>
        </div>

        <div class="adm-modal-body">
            @if($allMedia->count())
                <div class="adm-media-grid" id="mediaPickerGrid">
                    @foreach($allMedia as $m)
                    <div class="adm-media-item"
                         data-media-id="{{ $m->id }}"
                         data-media-url="{{ $m->url() }}"
                         data-media-path="{{ $m->path }}"
                         title="{{ $m->original_name }}">
                        <img class="adm-media-thumb" src="{{ $m->url() }}" alt="{{ $m->alt ?: $m->original_name }}" loading="lazy">
                        <div class="adm-media-meta">
                            <div class="adm-media-name">{{ $m->original_name }}</div>
                            <div class="adm-media-size">{{ $m->humanSize() }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="adm-empty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    <div class="adm-empty-title">No media uploaded yet</div>
                    <p>Upload an image using the button above.</p>
                </div>
            @endif
        </div>

        <div class="adm-modal-foot">
            <div class="adm-text-muted adm-text-sm" id="mediaPickerCount">
                {{ $allMedia->count() }} file(s) available
            </div>
            <div style="display:flex;gap:10px">
                <button type="button" class="adm-btn adm-btn-outline" id="mediaPickerClose2" onclick="document.getElementById('mediaPickerModal').classList.remove('open')">Cancel</button>
                <button type="button" class="adm-btn adm-btn-primary" id="mediaPickerConfirm" disabled>
                    <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Use Selected Image
                </button>
            </div>
        </div>
    </div>
</div>
