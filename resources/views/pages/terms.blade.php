@extends('layouts.app')

@section('seo_title', __('ui.terms.seo_title'))
@section('seo_desc',  __('ui.terms.seo_desc'))

@section('content')
<section class="terms-page">
    <div class="container">

        {{-- Hero Header --}}
        <div class="terms-hero">
            <div class="eyebrow">{{ __('ui.terms.eyebrow') }}</div>
            <h1>{{ __('ui.terms.title') }}</h1>
            <p>{{ __('ui.terms.sub') }}</p>
        </div>

        {{-- Main Terms Container --}}
        <div class="terms-body">

            {{-- Section 1: INCOTERMS --}}
            <div class="trade-terms-block">
                <div class="trade-terms-head">
                    <div class="trade-terms-icon">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                        </svg>
                    </div>
                    <h2>{{ __('ui.terms.incoterms_title') }}</h2>
                </div>

                <div class="trade-terms-grid">
                    <div class="trade-card">
                        <div class="trade-card-badge">{{ __('ui.terms.fob_code') }}</div>
                        <h3 class="trade-card-title">{{ __('ui.terms.fob_name') }}</h3>
                        <p class="trade-card-desc">{{ __('ui.terms.fob_desc') }}</p>
                    </div>

                    <div class="trade-card">
                        <div class="trade-card-badge">{{ __('ui.terms.cfr_code') }}</div>
                        <h3 class="trade-card-title">{{ __('ui.terms.cfr_name') }}</h3>
                        <p class="trade-card-desc">{{ __('ui.terms.cfr_desc') }}</p>
                    </div>

                    <div class="trade-card">
                        <div class="trade-card-badge">{{ __('ui.terms.cif_code') }}</div>
                        <h3 class="trade-card-title">{{ __('ui.terms.cif_name') }}</h3>
                        <p class="trade-card-desc">{{ __('ui.terms.cif_desc') }}</p>
                    </div>
                </div>

                <div class="trade-terms-note">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <span><em>{{ __('ui.terms.incoterms_note') }}</em></span>
                </div>
            </div>

            {{-- Section 2: PAYMENT TERMS --}}
            <div class="trade-terms-block">
                <div class="trade-terms-head">
                    <div class="trade-terms-icon">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                            <line x1="1" y1="10" x2="23" y2="10"></line>
                        </svg>
                    </div>
                    <h2>{{ __('ui.terms.payment_title') }}</h2>
                </div>

                <div class="trade-terms-grid trade-terms-grid-2">
                    <div class="trade-card">
                        <div class="trade-card-badge">{{ __('ui.terms.tt_code') }}</div>
                        <h3 class="trade-card-title">{{ __('ui.terms.tt_name') }}</h3>
                        <p class="trade-card-desc">{{ __('ui.terms.tt_desc') }}</p>
                    </div>

                    <div class="trade-card">
                        <div class="trade-card-badge">{{ __('ui.terms.lc_code') }}</div>
                        <h3 class="trade-card-title">{{ __('ui.terms.lc_name') }}</h3>
                        <p class="trade-card-desc">{{ __('ui.terms.lc_desc') }}</p>
                    </div>
                </div>

                <div class="trade-terms-note">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <span><em>{{ __('ui.terms.payment_note') }}</em></span>
                </div>
            </div>

        </div>

        {{-- Back Navigation --}}
        <div class="terms-back">
            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="btn btn-outline">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:6px;vertical-align:middle">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
                {{ __('ui.terms.back') }}
            </a>
        </div>

    </div>
</section>
@endsection
