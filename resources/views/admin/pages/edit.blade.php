@extends('admin.layouts.admin')

@section('title', 'Edit ' . $config['label'] . ' Content')
@section('page-title', 'Edit Content: ' . $config['label'])
@section('breadcrumb')
    <span class="adm-breadcrumb-sep">/</span>
    <a href="{{ route('admin.pages.index') }}">Pages</a>
    <span class="adm-breadcrumb-sep">/</span>
    <span>{{ $config['label'] }}</span>
@endsection

@section('content')
<div class="adm-card" style="max-width: 960px; margin: 0 auto;">
    <div class="adm-card-header">
        <div class="adm-card-title">{{ $config['label'] }} Page Sections</div>
        <div class="adm-flex adm-gap-8">
            <a class="adm-btn adm-btn-outline adm-btn-sm" href="{{ route('admin.pages.index') }}">Back to Pages</a>
            <a class="adm-btn adm-btn-outline adm-btn-sm" href="{{ $page === 'home' ? url('/') : url('/' . ($page === 'about' ? 'about-us' : ($page === 'contact' ? 'contact-us' : $page))) }}" target="_blank">
                View Live Page
            </a>
        </div>
    </div>

    <div class="adm-card-body">
        <form method="POST" action="{{ route('admin.pages.update', $page) }}" data-single-submit>
            @csrf
            @method('PUT')

            @foreach($config['sections'] as $secKey => $sec)
                <div class="adm-card adm-mb-24" style="border: 1px solid var(--adm-border); box-shadow: none;">
                    <div class="adm-card-header" style="background: var(--adm-surface);">
                        <div class="adm-card-title" style="font-size: 13.5px; text-transform: uppercase; letter-spacing: 0.05em;">
                            {{ $sec['label'] }}
                        </div>
                    </div>

                    <div class="adm-card-body">
                        <div class="adm-form-grid">
                            @foreach($sec['fields'] as $fld)
                                @php
                                    $inputName = $secKey . '__' . $fld;
                                    $currentVal = $values[$secKey][$fld] ?? '';
                                    $isLong = in_array($fld, ['description', 'paragraph1', 'paragraph2', 'paragraph3', 'body', 'subtitle', 'vision', 'mission']);
                                @endphp

                                <div class="adm-field {{ $isLong ? 'full' : '' }}">
                                    <label class="adm-label" for="{{ $inputName }}">
                                        {{ ucwords(str_replace('_', ' ', $fld)) }}
                                    </label>

                                    @if($isLong)
                                        <textarea class="adm-textarea" id="{{ $inputName }}" name="{{ $inputName }}" rows="3">{{ old($inputName, $currentVal) }}</textarea>
                                    @else
                                        <input class="adm-input" type="text" id="{{ $inputName }}" name="{{ $inputName }}" value="{{ old($inputName, $currentVal) }}">
                                    @endif
                                </div>
                            @endforeach

                            {{-- Optional Section Image Selector --}}
                            @php
                                $imgMedia = $mediaMap[$secKey]['image'] ?? null;
                            @endphp
                            @include('admin.partials.image-field', [
                                'label'         => $sec['label'] . ' Image (Optional)',
                                'groupName'     => $secKey . '_img',
                                'mediaIdName'   => $secKey . '__image__media_id',
                                'mediaPathName' => $secKey . '__image__path',
                                'currentUrl'    => $imgMedia ? $imgMedia->url() : null,
                                'required'      => false,
                            ])
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="adm-flex adm-gap-12" style="justify-content: flex-end;">
                <a class="adm-btn adm-btn-outline" href="{{ route('admin.pages.index') }}">Cancel</a>
                <button type="submit" class="adm-btn adm-btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('modals')
    @include('admin.partials.media-picker-modal', ['allMedia' => $allMedia])
@endpush
