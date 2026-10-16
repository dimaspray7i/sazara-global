@extends('admin.layouts.admin')

@section('title', 'Edit Gallery: ' . $gallery->title)
@section('page-title', 'Edit Gallery')
@section('breadcrumb')
    <span class="adm-breadcrumb-sep">/</span>
    <a href="{{ route('admin.gallery.index') }}">Gallery</a>
    <span class="adm-breadcrumb-sep">/</span>
    <span>Edit</span>
@endsection

@section('content')

@if(session('success'))
    <div class="adm-alert adm-alert-success adm-mb-16">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="adm-alert adm-alert-danger adm-mb-16">
        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
    </div>
@endif

<form method="POST" action="{{ route('admin.gallery.update', $gallery) }}" enctype="multipart/form-data" id="editGalleryForm">
    @csrf @method('PUT')

    {{-- Hidden: media order (filled by JS drag-drop) --}}
    <input type="hidden" name="media_order" id="mediaOrderInput">

    <div class="adm-grid-2 adm-gap-24" style="align-items: start;">

        {{-- Left: Info & Media Manager --}}
        <div style="display: flex; flex-direction: column; gap: 20px;">

            <div class="adm-card">
                <div class="adm-card-header"><div class="adm-card-title">Gallery Information</div></div>
                <div class="adm-card-body" style="display: flex; flex-direction: column; gap: 14px;">

                    <div class="adm-grid-2 adm-gap-12">
                        <div class="adm-form-group">
                            <label class="adm-label">Title (EN) <span style="color:red">*</span></label>
                            <input type="text" name="title" class="adm-input" value="{{ old('title', $gallery->title) }}" required>
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-label">Judul (ID)</label>
                            <input type="text" name="title_id" class="adm-input" value="{{ old('title_id', $gallery->title_id) }}">
                        </div>
                    </div>

                    <div class="adm-grid-2 adm-gap-12">
                        <div class="adm-form-group">
                            <label class="adm-label">Description (EN)</label>
                            <textarea name="description" class="adm-input" rows="3">{{ old('description', $gallery->description) }}</textarea>
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-label">Deskripsi (ID)</label>
                            <textarea name="description_id" class="adm-input" rows="3">{{ old('description_id', $gallery->description_id) }}</textarea>
                        </div>
                    </div>

                    <div class="adm-grid-2 adm-gap-12">
                        <div class="adm-form-group">
                            <label class="adm-label">Category</label>
                            <select name="category_slug" class="adm-select">
                                <option value="">— None —</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->slug }}" {{ old('category_slug', $gallery->category_slug) === $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-label">Sort Order</label>
                            <input type="number" name="sort_order" class="adm-input" value="{{ old('sort_order', $gallery->sort_order) }}" min="0">
                        </div>
                    </div>

                    <div class="adm-grid-2 adm-gap-12">
                        <div class="adm-form-group">
                            <label class="adm-label">Gallery Type</label>
                            <select name="type" class="adm-select">
                                <option value="single" {{ old('type', $gallery->type) === 'single' ? 'selected' : '' }}>Single</option>
                                <option value="group" {{ old('type', $gallery->type) === 'group' ? 'selected' : '' }}>Group</option>
                            </select>
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-label">Status</label>
                            <select name="status" class="adm-select">
                                <option value="draft" {{ old('status', $gallery->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status', $gallery->status) === 'published' ? 'selected' : '' }}>Published</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Current Media --}}
            <div class="adm-card">
                <div class="adm-card-header">
                    <div class="adm-card-title">Current Media ({{ $gallery->media->count() }})</div>
                    <small style="color:#888;font-size:11px;">Drag to reorder. Click 📌 to set cover. Click ✕ to remove from gallery (file kept).</small>
                </div>
                <div class="adm-card-body">
                    @if($gallery->media->count())
                        <div id="mediaSortable" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 10px;">
                            @foreach($gallery->media as $m)
                                <div class="gallery-media-item" data-id="{{ $m->id }}" style="position:relative;border-radius:8px;overflow:hidden;background:#eee;aspect-ratio:1;cursor:grab;border: {{ $gallery->cover_media_id == $m->id ? '3px solid #3C8C2B' : '2px solid transparent' }};">

                                    @if(str_starts_with($m->mime_type, 'video/'))
                                        <div style="width:100%;height:100%;position:relative;background:#000;cursor:pointer;" onclick="playAdminVideo('{{ $m->url() }}')">
                                            <video src="{{ $m->url() }}" style="width:100%;height:100%;object-fit:cover;display:block;" muted preload="metadata"></video>
                                            <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.35);" title="Click to play video">
                                                <svg viewBox="0 0 24 24" fill="white" width="28" height="28"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                            </div>
                                        </div>
                                    @else
                                        <img src="{{ $m->url() }}" style="width:100%;height:100%;object-fit:cover;" loading="lazy">
                                    @endif

                                    {{-- Cover badge --}}
                                    @if($gallery->cover_media_id == $m->id)
                                        <div class="sg-cover-badge" style="position:absolute;top:3px;left:3px;background:#3C8C2B;color:#fff;font-size:8px;padding:1px 5px;border-radius:3px;">COVER</div>
                                    @endif

                                    {{-- Actions --}}
                                    <div style="position:absolute;bottom:0;left:0;right:0;background:rgba(0,0,0,0.65);display:flex;justify-content:space-between;padding:3px 4px;">
                                        {{-- Set cover --}}
                                        <button type="button" class="sg-pin-btn" title="Set as cover"
                                            onclick="setCover({{ $gallery->id }}, {{ $m->id }}, this)"
                                            style="background:none;border:none;color:#FFD700;cursor:pointer;font-size:12px;padding:0;" {{ $gallery->cover_media_id == $m->id ? 'disabled' : '' }}>
                                            📌
                                        </button>
                                        {{-- Remove --}}
                                        <button type="button" title="Remove from gallery"
                                            onclick="removeMedia({{ $gallery->id }}, {{ $m->id }}, this.closest('.gallery-media-item'))"
                                            style="background:none;border:none;color:#ff6b6b;cursor:pointer;font-size:12px;padding:0;">
                                            ✕
                                        </button>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                        <p style="font-size:11px;color:#888;margin-top:8px;">Reorder by dragging. Save the form to persist changes.</p>
                    @else
                        <p style="color:#aaa;font-size:13px;">No media added yet. Upload below.</p>
                    @endif
                </div>
            </div>

        </div>

        {{-- Right: Add more media --}}
        <div style="display: flex; flex-direction: column; gap: 20px;">

            {{-- Cover preview --}}
            <div class="adm-card">
                <div class="adm-card-header"><div class="adm-card-title">Cover</div></div>
                <div class="adm-card-body" style="text-align:center;">
                    <input type="hidden" name="cover_media_id" id="coverMediaId" value="{{ $gallery->cover_media_id }}">
                    @if($gallery->coverMedia)
                        @if(str_starts_with($gallery->coverMedia->mime_type, 'video/'))
                            <video id="coverPreview" src="{{ $gallery->coverMedia->url() }}" style="max-width:100%;border-radius:8px;max-height:200px;object-fit:cover;" controls muted preload="metadata"></video>
                        @else
                            <img id="coverPreview" src="{{ $gallery->coverMedia->url() }}" style="max-width:100%;border-radius:8px;max-height:200px;object-fit:cover;" alt="Cover">
                        @endif
                    @else
                        <div id="coverPreview" style="width:100%;height:120px;background:#eee;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#aaa;font-size:12px;">No cover set</div>
                    @endif
                </div>
            </div>

            {{-- Upload more photos --}}
            <div class="adm-card">
                <div class="adm-card-header"><div class="adm-card-title">Add More Photos/Videos</div></div>
                <div class="adm-card-body">
                    <div class="adm-drop-zone" style="cursor: pointer; position: relative;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="28" height="28"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        <div class="adm-drop-zone-text">Click or drag images/videos</div>
                        <div class="adm-drop-zone-hint">Multiple files. Max 10 MB each.</div>
                        <input type="file" name="photos[]" id="newPhotos" multiple accept="image/jpeg,image/png,image/webp,video/mp4,video/quicktime" style="position:absolute;inset:0;opacity:0;cursor:pointer;" onchange="previewNewPhotos(this)">
                    </div>
                    <div id="newPhotoPreview" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(60px,1fr));gap:6px;margin-top:10px;"></div>
                </div>
            </div>

            {{-- Upload ZIP --}}
            <div class="adm-card">
                <div class="adm-card-header"><div class="adm-card-title">Add from ZIP</div></div>
                <div class="adm-card-body">
                    <div class="adm-drop-zone" style="cursor: pointer; position: relative;">
                        <div class="adm-drop-zone-text">Click or drag ZIP file</div>
                        <div class="adm-drop-zone-hint">Max 100 MB, max 100 files.</div>
                        <input type="file" name="zip_file" accept=".zip,application/zip" style="position:absolute;inset:0;opacity:0;cursor:pointer;" onchange="document.getElementById('zipDisplay').textContent = '📦 ' + this.files[0].name">
                    </div>
                    <div id="zipDisplay" style="margin-top:8px;font-size:12px;color:#555;"></div>
                </div>
            </div>

        </div>
    </div>

    <div class="adm-flex adm-gap-12" style="margin-top: 20px;">
        <button type="submit" class="adm-btn adm-btn-primary">Save Changes</button>
        <a href="{{ route('admin.gallery.index') }}" class="adm-btn adm-btn-outline">Back to Gallery</a>
        <button type="submit" form="toggleGalleryForm" class="adm-btn {{ $gallery->status === 'published' ? 'adm-btn-outline' : 'adm-btn-primary' }}">
            {{ $gallery->status === 'published' ? 'Unpublish' : 'Publish' }}
        </button>
    </div>

</form>

<form id="toggleGalleryForm" method="POST" action="{{ route('admin.gallery.toggle', $gallery) }}" style="display:none;">
    @csrf
</form>

{{-- Admin HTML5 Video Player Modal --}}
<div id="adminVideoModal" style="display:none;position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,0.85);align-items:center;justify-content:center;padding:20px;" onclick="if(event.target===this)closeAdminVideo();">
    <div style="position:relative;max-width:860px;width:100%;background:#000;border-radius:12px;overflow:hidden;box-shadow:0 12px 48px rgba(0,0,0,0.8);">
        <button type="button" onclick="closeAdminVideo()" style="position:absolute;top:12px;right:12px;z-index:10;background:rgba(255,255,255,0.25);color:#fff;border:none;border-radius:50%;width:34px;height:34px;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .2s;" onmouseover="this.style.background='rgba(255,255,255,0.4)'" onmouseout="this.style.background='rgba(255,255,255,0.25)'">✕</button>
        <video id="adminModalVideoPlayer" controls playsinline style="width:100%;max-height:80vh;display:block;outline:none;background:#000;"></video>
    </div>
</div>

@push('head')
<script>
const GALLERY_ID = {{ $gallery->id }};
const CSRF = document.querySelector('meta[name=csrf-token]').content;
// Route URLs generated server-side — correct regardless of APP_URL or subfolder
const COVER_URL   = '{{ route('admin.gallery.setCover', $gallery) }}';
const REORDER_URL = '{{ route('admin.gallery.reorder', $gallery) }}';
const REMOVE_BASE = '{{ url('admin/gallery/' . $gallery->id . '/media') }}';

// Toast notification helper
function showToast(msg, ok = true) {
    let t = document.getElementById('sgToast');
    if (!t) {
        t = document.createElement('div');
        t.id = 'sgToast';
        t.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:9999;padding:10px 18px;border-radius:8px;font-size:13px;font-weight:600;color:#fff;box-shadow:0 4px 16px rgba(0,0,0,.25);transition:opacity .3s;';
        document.body.appendChild(t);
    }
    t.textContent = msg;
    t.style.background = ok ? '#3C8C2B' : '#dc3545';
    t.style.opacity = '1';
    clearTimeout(t._tid);
    t._tid = setTimeout(() => { t.style.opacity = '0'; }, 2800);
}

// Drag-to-reorder (simple mouse-based sortable without library dependency)
let dragging = null;
document.querySelectorAll('.gallery-media-item').forEach(el => {
    el.addEventListener('dragstart', e => { dragging = el; el.style.opacity = '0.5'; });
    el.addEventListener('dragend', e => { el.style.opacity = '1'; saveOrder(); });
    el.addEventListener('dragover', e => { e.preventDefault(); });
    el.addEventListener('drop', e => {
        e.preventDefault();
        if (dragging && dragging !== el) {
            const grid = document.getElementById('mediaSortable');
            const items = Array.from(grid.children);
            const fromIdx = items.indexOf(dragging);
            const toIdx = items.indexOf(el);
            if (fromIdx < toIdx) el.after(dragging); else el.before(dragging);
            saveOrder();
        }
    });
    el.setAttribute('draggable', true);
});

function saveOrder() {
    const ids = Array.from(document.querySelectorAll('.gallery-media-item')).map(el => el.dataset.id);
    document.getElementById('mediaOrderInput').value = JSON.stringify(ids);
    // AJAX-save reorder using server-generated URL
    fetch(REORDER_URL, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ order: ids })
    });
}

function removeMedia(galleryId, mediaId, el) {
    if (!confirm('Remove this media from the gallery? The file will NOT be deleted.')) return;
    fetch(REMOVE_BASE + '/' + mediaId, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    }).then(r => r.json()).then(data => {
        if (data.success) {
            el.remove();
            showToast('Media removed from gallery.');
        } else {
            showToast(data.message || 'Failed to remove.', false);
        }
    }).catch(() => showToast('Network error.', false));
}

function setCover(galleryId, mediaId, btn) {
    fetch(COVER_URL, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ media_id: mediaId })
    }).then(r => r.json()).then(data => {
        if (data.success) {
            document.getElementById('coverMediaId').value = mediaId;

            // Remove old cover styling from all items
            document.querySelectorAll('.gallery-media-item').forEach(item => {
                item.style.border = '2px solid transparent';
                const badge = item.querySelector('.sg-cover-badge');
                if (badge) badge.remove();
                const pinBtn = item.querySelector('.sg-pin-btn');
                if (pinBtn) pinBtn.disabled = false;
            });

            // Apply cover styling to selected item
            const item = btn.closest('.gallery-media-item');
            item.style.border = '3px solid #3C8C2B';
            btn.disabled = true;

            // Add COVER badge
            const badge = document.createElement('div');
            badge.className = 'sg-cover-badge';
            badge.style.cssText = 'position:absolute;top:3px;left:3px;background:#3C8C2B;color:#fff;font-size:8px;padding:1px 5px;border-radius:3px;';
            badge.textContent = 'COVER';
            item.appendChild(badge);

            // Update cover preview panel
            const coverEl = document.getElementById('coverPreview');
            if (data.is_video) {
                // Replace preview with a video element
                const vid = document.createElement('video');
                vid.src = data.media_url;
                vid.style.cssText = 'max-width:100%;border-radius:8px;max-height:200px;object-fit:cover;';
                vid.controls = true;
                vid.muted = true;
                vid.preload = 'metadata';
                coverEl.replaceWith(vid);
                vid.id = 'coverPreview';
            } else {
                if (coverEl.tagName === 'VIDEO') {
                    const img = document.createElement('img');
                    img.src = data.media_url;
                    img.style.cssText = 'max-width:100%;border-radius:8px;max-height:200px;object-fit:cover;';
                    img.alt = 'Cover';
                    coverEl.replaceWith(img);
                    img.id = 'coverPreview';
                } else {
                    coverEl.src = data.media_url;
                }
            }
            showToast('Cover updated successfully!');
        } else {
            showToast(data.message || 'Failed to set cover.', false);
        }
    }).catch(() => showToast('Network error — could not set cover.', false));
}

function previewNewPhotos(input) {
    const grid = document.getElementById('newPhotoPreview');
    grid.innerHTML = '';
    Array.from(input.files).forEach(file => {
        const div = document.createElement('div');
        div.style.cssText = 'aspect-ratio:1;overflow:hidden;border-radius:5px;background:#eee;';
        if (file.type.startsWith('image/')) {
            const img = document.createElement('img');
            img.style.cssText = 'width:100%;height:100%;object-fit:cover;';
            img.src = URL.createObjectURL(file);
            div.appendChild(img);
        } else {
            div.innerHTML = '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:24px;">▶</div>';
        }
        grid.appendChild(div);
    });
}

function playAdminVideo(url) {
    const modal = document.getElementById('adminVideoModal');
    const player = document.getElementById('adminModalVideoPlayer');
    if (!modal || !player) return;
    player.src = url;
    modal.style.display = 'flex';
    player.play().catch(e => console.log('Autoplay deferred:', e));
}

function closeAdminVideo() {
    const modal = document.getElementById('adminVideoModal');
    const player = document.getElementById('adminModalVideoPlayer');
    if (!modal || !player) return;
    try { player.pause(); player.src = ''; } catch(e){}
    modal.style.display = 'none';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeAdminVideo();
});
</script>
@endpush
@endsection
