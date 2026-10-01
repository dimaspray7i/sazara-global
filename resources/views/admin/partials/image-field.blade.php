{{--
    Reusable Media Image Field
    Usage:
      @include('admin.partials.image-field', [
        'label'       => 'Product Image',
        'groupName'   => 'product_image',      // unique per form
        'mediaIdName' => 'media_id_input_name', // hidden input name submitted with form (for page sections)
        'mediaPathName' => 'media_path',         // hidden input name for path (for products/articles)
        'currentUrl'  => $product->imageUrl(),  // current preview URL or null
        'required'    => false,
      ])
--}}

@php
    $groupName    = $groupName    ?? 'image_field';
    $label        = $label        ?? 'Image';
    $mediaIdName  = $mediaIdName  ?? null;
    $mediaPathName= $mediaPathName ?? 'media_path';
    $currentUrl   = $currentUrl   ?? null;
    $required     = $required     ?? false;
    $previewId    = 'prev_' . $groupName;
    $fileInputId  = 'file_' . $groupName;
@endphp

<div class="adm-field full">
    <label class="adm-label">{{ $label }}@if($required)<span class="req">*</span>@endif</label>

    <div data-media-group="{{ $groupName }}">

        {{-- Hidden inputs --}}
        @if($mediaIdName)
        <input type="hidden" name="{{ $mediaIdName }}" data-media-id-input value="">
        @endif
        <input type="hidden" name="{{ $mediaPathName }}" data-media-path-input value="">

        {{-- Preview --}}
        <div class="adm-image-preview" style="margin-bottom:12px">
            @if($currentUrl)
                <img id="{{ $previewId }}" src="{{ $currentUrl }}" alt="Current image" data-media-preview>
            @else
                <div class="adm-image-preview-placeholder">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    <span>No image selected</span>
                </div>
                <img id="{{ $previewId }}" src="" alt="" data-media-preview style="display:none;width:100%;height:100%;object-fit:cover">
            @endif
        </div>

        {{-- Selected media label --}}
        <div class="adm-image-info adm-mb-16">
            <span data-media-label>{{ $currentUrl ? 'Current image' : 'No image selected' }}</span>
        </div>

        {{-- Action buttons --}}
        <div class="adm-image-actions">
            {{-- Choose from Media Library --}}
            <button type="button" class="adm-btn adm-btn-outline adm-btn-sm" data-open-picker>
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                Choose from Media Library
            </button>

            {{-- Direct upload --}}
            <label class="adm-btn adm-btn-outline adm-btn-sm" style="cursor:pointer">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Upload New
                <input type="file" id="{{ $fileInputId }}" name="image_file" accept="image/jpeg,image/png,image/webp"
                       data-preview-for="{{ $previewId }}" style="display:none">
            </label>
        </div>

        <div class="adm-hint adm-mt-16">Accepted: JPG, PNG, WEBP — Max 5 MB</div>

        @if($currentUrl)
        <div style="margin-top:10px">
            <label class="adm-toggle-row">
                <div class="adm-toggle">
                    <input type="checkbox" name="clear_image" value="1">
                    <div class="adm-toggle-track"></div>
                </div>
                <span class="adm-toggle-label">Remove current image</span>
            </label>
        </div>
        @endif

    </div>
</div>
