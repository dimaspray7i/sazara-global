@extends('admin.layouts.admin')

@section('title', 'Create Gallery')
@section('page-title', 'Create Gallery')
@section('breadcrumb')
    <span class="adm-breadcrumb-sep">/</span>
    <a href="{{ route('admin.gallery.index') }}">Gallery</a>
    <span class="adm-breadcrumb-sep">/</span>
    <span>Create</span>
@endsection

@section('content')

@if($errors->any())
    <div class="adm-alert adm-alert-danger adm-mb-16">
        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
    </div>
@endif

<form method="POST" action="{{ route('admin.gallery.store') }}" enctype="multipart/form-data" id="galleryForm">
    @csrf

    <div class="adm-grid-2 adm-gap-24" style="align-items: start;">

        {{-- Left: Info --}}
        <div style="display: flex; flex-direction: column; gap: 20px;">

            <div class="adm-card">
                <div class="adm-card-header"><div class="adm-card-title">Gallery Information</div></div>
                <div class="adm-card-body" style="display: flex; flex-direction: column; gap: 14px;">

                    <div class="adm-grid-2 adm-gap-12">
                        <div class="adm-form-group">
                            <label class="adm-label">Title (EN) <span style="color:red">*</span></label>
                            <input type="text" name="title" class="adm-input" value="{{ old('title') }}" placeholder="e.g. Palm Loading at Port" required>
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-label">Judul (ID)</label>
                            <input type="text" name="title_id" class="adm-input" value="{{ old('title_id') }}" placeholder="Contoh: Pemuatan Sawit di Pelabuhan">
                        </div>
                    </div>

                    <div class="adm-grid-2 adm-gap-12">
                        <div class="adm-form-group">
                            <label class="adm-label">Description (EN)</label>
                            <textarea name="description" class="adm-input" rows="3" placeholder="Short description...">{{ old('description') }}</textarea>
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-label">Deskripsi (ID)</label>
                            <textarea name="description_id" class="adm-input" rows="3" placeholder="Deskripsi singkat...">{{ old('description_id') }}</textarea>
                        </div>
                    </div>

                    <div class="adm-grid-2 adm-gap-12">
                        <div class="adm-form-group">
                            <label class="adm-label">Category</label>
                            <select name="category_slug" class="adm-select">
                                <option value="">— None —</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->slug }}" {{ old('category_slug') === $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-label">Sort Order</label>
                            <input type="number" name="sort_order" class="adm-input" value="{{ old('sort_order', 0) }}" min="0">
                        </div>
                    </div>

                    <div class="adm-grid-2 adm-gap-12">
                        <div class="adm-form-group">
                            <label class="adm-label">Gallery Type</label>
                            <select name="type" id="galleryType" class="adm-select" onchange="updateTypeHint()">
                                <option value="single" {{ old('type', 'single') === 'single' ? 'selected' : '' }}>Single Photo/Video</option>
                                <option value="group" {{ old('type') === 'group' ? 'selected' : '' }}>Group (multiple media)</option>
                            </select>
                            <small id="typeHint" style="color: #777; font-size: 11px; margin-top: 4px; display: block;">One media = one public card.</small>
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-label">Status</label>
                            <select name="status" class="adm-select">
                                <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft (hidden from public)</option>
                                <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        {{-- Right: Upload --}}
        <div style="display: flex; flex-direction: column; gap: 20px;">

            {{-- A: Single / Multi photo upload --}}
            <div class="adm-card">
                <div class="adm-card-header"><div class="adm-card-title">Upload Photos / Videos</div></div>
                <div class="adm-card-body">
                    <div class="adm-drop-zone" id="photoDropZone" style="cursor: pointer;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="32" height="32"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        <div class="adm-drop-zone-text">Click or drag images/videos here</div>
                        <div class="adm-drop-zone-hint">JPG, PNG, WEBP, MP4, MOV — Max 10 MB each. Multiple files allowed.</div>
                        <input type="file" name="photos[]" id="photoInput" multiple accept="image/jpeg,image/png,image/webp,video/mp4,video/quicktime" style="position:absolute;inset:0;opacity:0;cursor:pointer;">
                    </div>
                    <div id="photoPreviewGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); gap: 8px; margin-top: 12px;"></div>
                </div>
            </div>

            {{-- B: ZIP upload --}}
            <div class="adm-card">
                <div class="adm-card-header"><div class="adm-card-title">Upload ZIP Archive</div></div>
                <div class="adm-card-body">
                    <div class="adm-drop-zone" style="cursor: pointer; position: relative;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="28" height="28"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        <div class="adm-drop-zone-text">Click or drag ZIP file here</div>
                        <div class="adm-drop-zone-hint">ZIP containing images/videos. Max 100 MB, max 100 files. Encrypted ZIPs not supported.</div>
                        <input type="file" name="zip_file" id="zipInput" accept=".zip,application/zip" style="position:absolute;inset:0;opacity:0;cursor:pointer;" onchange="showZipName(this)">
                    </div>
                    <div id="zipNameDisplay" style="margin-top: 8px; font-size: 12px; color: #555;"></div>
                </div>
            </div>

        </div>

    </div>

    {{-- Submit --}}
    <div class="adm-flex adm-gap-12" style="margin-top: 20px;">
        <button type="submit" class="adm-btn adm-btn-primary">Create Gallery</button>
        <a href="{{ route('admin.gallery.index') }}" class="adm-btn adm-btn-outline">Cancel</a>
    </div>

</form>

@push('head')
<script>
function updateTypeHint() {
    const type = document.getElementById('galleryType').value;
    const hint = document.getElementById('typeHint');
    hint.textContent = type === 'group'
        ? 'Multiple media → one card on public gallery. Click to view all.'
        : 'One media → one card. Click opens the photo/video directly.';
}
function showZipName(input) {
    const el = document.getElementById('zipNameDisplay');
    if (input.files[0]) {
        el.textContent = '📦 ' + input.files[0].name + ' (' + (input.files[0].size / 1024 / 1024).toFixed(1) + ' MB)';
    }
}

// Photo preview on select
document.getElementById('photoInput').addEventListener('change', function() {
    const grid = document.getElementById('photoPreviewGrid');
    grid.innerHTML = '';
    Array.from(this.files).forEach(function(file) {
        const div = document.createElement('div');
        div.style.cssText = 'position:relative;aspect-ratio:1;overflow:hidden;border-radius:6px;background:#eee;';
        if (file.type.startsWith('image/')) {
            const img = document.createElement('img');
            img.style.cssText = 'width:100%;height:100%;object-fit:cover;';
            img.src = URL.createObjectURL(file);
            div.appendChild(img);
        } else {
            div.innerHTML = '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:10px;color:#555;">▶ Video</div>';
        }
        const label = document.createElement('div');
        label.style.cssText = 'position:absolute;bottom:0;left:0;right:0;background:rgba(0,0,0,0.55);color:#fff;font-size:9px;padding:2px 4px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;';
        label.textContent = file.name;
        div.appendChild(label);
        grid.appendChild(div);
    });
});
</script>
@endpush
@endsection
