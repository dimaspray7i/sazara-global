@extends('admin.layouts.admin')

@php
    $isEdit = $product->exists;
@endphp

@section('title', $isEdit ? 'Edit Product: ' . $product->name : 'Add New Product')
@section('page-title', $isEdit ? 'Edit Product' : 'Add New Product')
@section('breadcrumb')
    <span class="adm-breadcrumb-sep">/</span>
    <a href="{{ route('admin.products.index') }}">Products</a>
    <span class="adm-breadcrumb-sep">/</span>
    <span>{{ $isEdit ? $product->name : 'New' }}</span>
@endsection

@section('content')
<div class="adm-card" style="max-width: 900px; margin: 0 auto;">
    <div class="adm-card-header">
        <div class="adm-card-title">{{ $isEdit ? 'Edit: ' . $product->name : 'Create New Product' }}</div>
        <a class="adm-btn adm-btn-outline adm-btn-sm" href="{{ route('admin.products.index') }}">Back to List</a>
    </div>

    <div class="adm-card-body">
        <form method="POST" action="{{ $isEdit ? route('admin.products.update', $product) : route('admin.products.store') }}" enctype="multipart/form-data" data-single-submit>
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="adm-form-grid">
                {{-- Product Name (EN & ID) --}}
                <div class="adm-form-grid-2">
                    <div class="adm-field">
                        <label class="adm-label" for="fieldName">Product Name (EN) <span class="req">*</span></label>
                        <input class="adm-input" type="text" id="fieldName" name="name" value="{{ old('name', $product->name) }}" required placeholder="e.g. Palm Broom">
                    </div>
                    <div class="adm-field">
                        <label class="adm-label" for="name_id">Product Name (ID - Optional)</label>
                        <input class="adm-input" type="text" id="name_id" name="name_id" value="{{ old('name_id', $product->name_id) }}" placeholder="e.g. Sapu Lidi Sawit">
                    </div>
                </div>

                {{-- Slug --}}
                <div class="adm-field">
                    <label class="adm-label" for="fieldSlug">Slug (URL identifier)</label>
                    <input class="adm-input" type="text" id="fieldSlug" name="slug" value="{{ old('slug', $product->slug) }}" placeholder="auto-generated-from-name">
                    <span class="adm-hint">Public URL: {{ url('/products') }}/<strong>your-slug</strong></span>
                </div>

                {{-- Descriptions --}}
                <div class="adm-form-grid-2">
                    <div class="adm-field">
                        <label class="adm-label" for="description">Description (EN) <span class="req">*</span></label>
                        <textarea class="adm-textarea" id="description" name="description" required placeholder="Describe the commodity, applications, and source...">{{ old('description', $product->description) }}</textarea>
                    </div>
                    <div class="adm-field">
                        <label class="adm-label" for="description_id">Description (ID - Optional)</label>
                        <textarea class="adm-textarea" id="description_id" name="description_id" placeholder="Deskripsi dalam Bahasa Indonesia...">{{ old('description_id', $product->description_id) }}</textarea>
                    </div>
                </div>

                {{-- Specifications --}}
                <div class="adm-form-grid-2">
                    <div class="adm-field">
                        <label class="adm-label" for="specification">Specification (EN - Optional)</label>
                        <textarea class="adm-textarea" id="specification" name="specification" placeholder="e.g. Length: 90-100cm, Moisture: <15%...">{{ old('specification', $product->specification) }}</textarea>
                    </div>
                    <div class="adm-field">
                        <label class="adm-label" for="specification_id">Specification (ID - Optional)</label>
                        <textarea class="adm-textarea" id="specification_id" name="specification_id" placeholder="Spesifikasi teknis dalam Bahasa Indonesia...">{{ old('specification_id', $product->specification_id) }}</textarea>
                    </div>
                </div>

                {{-- Image Field (Reusable Media Selector / Direct Upload) --}}
                @include('admin.partials.image-field', [
                    'label'         => 'Product Image',
                    'groupName'     => 'product_image',
                    'mediaPathName' => 'media_path',
                    'currentUrl'    => $isEdit ? $product->imageUrl() : null,
                    'required'      => false,
                ])

                {{-- WhatsApp Custom Message & Sort Order --}}
                <div class="adm-form-grid-3">
                    <div class="adm-field" style="grid-column: span 2;">
                        <label class="adm-label" for="wa_template">WhatsApp Inquiry Message (Optional)</label>
                        <input class="adm-input" type="text" id="wa_template" name="wa_template" value="{{ old('wa_template', $product->wa_template) }}" placeholder="Custom message when buyer clicks Get Offer">
                    </div>
                    <div class="adm-field">
                        <label class="adm-label" for="sort_order">Sort Order</label>
                        <input class="adm-input" type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $product->sort_order ?? 0) }}" min="0">
                    </div>
                </div>

                {{-- Toggles --}}
                <div class="adm-form-grid-2 adm-mt-16">
                    <label class="adm-toggle-row">
                        <div class="adm-toggle">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
                            <div class="adm-toggle-track"></div>
                        </div>
                        <span class="adm-toggle-label">Active (Visible on public website)</span>
                    </label>

                    <label class="adm-toggle-row">
                        <div class="adm-toggle">
                            <input type="hidden" name="is_featured" value="0">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}>
                            <div class="adm-toggle-track"></div>
                        </div>
                        <span class="adm-toggle-label">Featured on Homepage</span>
                    </label>
                </div>
            </div>

            <div class="adm-divider"></div>

            <div class="adm-flex adm-gap-12" style="justify-content: flex-end;">
                <a class="adm-btn adm-btn-outline" href="{{ route('admin.products.index') }}">Cancel</a>
                <button type="submit" class="adm-btn adm-btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ $isEdit ? 'Save Changes' : 'Create Product' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('modals')
    @include('admin.partials.media-picker-modal', ['allMedia' => $media])
@endpush
