@extends('admin.layouts.admin')

@section('title', 'Gallery Categories')
@section('page-title', 'Gallery Categories')
@section('breadcrumb')
    <span class="adm-breadcrumb-sep">/</span>
    <a href="{{ route('admin.gallery.index') }}">Gallery</a>
    <span class="adm-breadcrumb-sep">/</span>
    <span>Categories</span>
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

<div class="adm-grid-2 adm-gap-24" style="align-items: start;">

    {{-- Add Category Form --}}
    <div class="adm-card">
        <div class="adm-card-header"><div class="adm-card-title">Add Category</div></div>
        <div class="adm-card-body">
            <form method="POST" action="{{ route('admin.gallery.categories.store') }}" style="display: flex; flex-direction: column; gap: 14px;">
                @csrf
                <div class="adm-form-group">
                    <label class="adm-label">Name (EN) <span style="color:red">*</span></label>
                    <input type="text" name="name" class="adm-input" value="{{ old('name') }}" placeholder="e.g. Warehouse & Storage" required>
                </div>
                <div class="adm-form-group">
                    <label class="adm-label">Nama (ID)</label>
                    <input type="text" name="name_id" class="adm-input" value="{{ old('name_id') }}" placeholder="Contoh: Gudang & Penyimpanan">
                </div>
                <div class="adm-form-group">
                    <label class="adm-label">Slug <span style="color:red">*</span></label>
                    <input type="text" name="slug" id="slugInput" class="adm-input" value="{{ old('slug') }}" placeholder="e.g. warehouse" pattern="[a-z0-9_-]+" required>
                    <small style="color:#777;font-size:11px;">Lowercase, no spaces. Use - or _.</small>
                </div>
                <div class="adm-form-group">
                    <label class="adm-label">Sort Order</label>
                    <input type="number" name="sort_order" class="adm-input" value="{{ old('sort_order', 0) }}" min="0">
                </div>
                <button type="submit" class="adm-btn adm-btn-primary">Add Category</button>
            </form>
        </div>
    </div>

    {{-- Category List --}}
    <div class="adm-card">
        <div class="adm-card-header"><div class="adm-card-title">Existing Categories ({{ $categories->count() }})</div></div>
        <div class="adm-card-body">
            @forelse($categories as $cat)
                <details class="adm-media-details" style="margin-bottom: 12px; border: 1px solid #e8e8e8; border-radius: 8px; padding: 10px;">
                    <summary style="display: flex; align-items: center; justify-content: space-between; cursor: pointer; font-weight: 600; font-size: 13px;">
                        <span>
                            {{ $cat->name }}
                            @if($cat->name_id)<span style="color:#888;font-weight:400;font-size:11px;margin-left:6px;">/ {{ $cat->name_id }}</span>@endif
                            <span class="adm-badge {{ $cat->is_active ? 'adm-badge-success' : 'adm-badge-muted' }}" style="font-size:10px;margin-left:6px;padding:1px 6px;">
                                {{ $cat->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </span>
                        <code style="font-size:11px;color:#555;">{{ $cat->slug }}</code>
                    </summary>

                    <form method="POST" action="{{ route('admin.gallery.categories.update', $cat) }}" style="margin-top: 12px; display: flex; flex-direction: column; gap: 10px;">
                        @csrf @method('PUT')
                        <div class="adm-grid-2 adm-gap-10">
                            <div class="adm-form-group">
                                <input type="text" name="name" class="adm-input" value="{{ $cat->name }}" placeholder="Name (EN)" style="font-size:12px;">
                            </div>
                            <div class="adm-form-group">
                                <input type="text" name="name_id" class="adm-input" value="{{ $cat->name_id }}" placeholder="Nama (ID)" style="font-size:12px;">
                            </div>
                        </div>
                        <div class="adm-grid-2 adm-gap-10">
                            <div class="adm-form-group">
                                <input type="number" name="sort_order" class="adm-input" value="{{ $cat->sort_order }}" placeholder="Sort order" style="font-size:12px;">
                            </div>
                            <div class="adm-form-group" style="display:flex;align-items:center;gap:8px;margin-top:18px;">
                                <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:12px;">
                                    <input type="checkbox" name="is_active" value="1" {{ $cat->is_active ? 'checked' : '' }}>
                                    Active
                                </label>
                            </div>
                        </div>
                        <div style="display:flex;gap:8px;">
                            <button type="submit" class="adm-btn adm-btn-primary adm-btn-sm">Save</button>
                            <form method="POST" action="{{ route('admin.gallery.categories.destroy', $cat) }}" onsubmit="return confirm('Delete category \'{{ addslashes($cat->name) }}\'? Galleries using this category will have their category cleared.')" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="adm-btn adm-btn-outline adm-btn-sm" style="color:#e53e3e;border-color:#e53e3e;">Delete</button>
                            </form>
                        </div>
                    </form>
                </details>
            @empty
                <p style="color:#aaa;font-size:13px;">No categories yet. Add one on the left.</p>
            @endforelse
        </div>
    </div>

</div>

@push('head')
<script>
// Auto-generate slug from name
document.querySelector('input[name=name]')?.addEventListener('input', function() {
    const slugInput = document.getElementById('slugInput');
    if (!slugInput.dataset.manual) {
        slugInput.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
    }
});
document.getElementById('slugInput')?.addEventListener('input', function() {
    this.dataset.manual = true;
});
</script>
@endpush
@endsection
