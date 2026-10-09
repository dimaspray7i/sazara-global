@extends('layouts.app')

@section('seo_title', __('ui.terms.seo_title'))
@section('seo_desc',  __('ui.terms.seo_desc'))

@section('content')
<section class="terms-page">
    <div class="container">

        {{-- Hero --}}
        <div class="terms-hero">
            <div class="eyebrow">{{ __('ui.footer.terms') }}</div>
            <h1>{{ __('ui.terms.title') }}</h1>
            <p>{{ __('ui.terms.effective') }}: <strong>{{ __('ui.terms.effective_date') }}</strong></p>
        </div>

        {{-- Content --}}
        <div class="terms-body">

            <div class="terms-intro">
                <p>{{ __('ui.terms.intro') }}</p>
            </div>

            <div class="terms-section">
                <h2>1. {{ __('ui.terms.s1_title') }}</h2>
                <p>{{ __('ui.terms.s1_body') }}</p>
            </div>

            <div class="terms-section">
                <h2>2. {{ __('ui.terms.s2_title') }}</h2>
                <p>{{ __('ui.terms.s2_body') }}</p>
            </div>

            <div class="terms-section">
                <h2>3. {{ __('ui.terms.s3_title') }}</h2>
                <p>{{ __('ui.terms.s3_body') }}</p>
            </div>

            <div class="terms-section">
                <h2>4. {{ __('ui.terms.s4_title') }}</h2>
                <p>{{ __('ui.terms.s4_body') }}</p>
            </div>

            <div class="terms-section">
                <h2>5. {{ __('ui.terms.s5_title') }}</h2>
                <p>{{ __('ui.terms.s5_body') }}</p>
            </div>

            <div class="terms-section">
                <h2>6. {{ __('ui.terms.s6_title') }}</h2>
                <p>{{ __('ui.terms.s6_body') }}</p>
            </div>

            <div class="terms-section">
                <h2>7. {{ __('ui.terms.s7_title') }}</h2>
                <p>{{ __('ui.terms.s7_body') }}</p>
            </div>

            <div class="terms-section">
                <h2>8. {{ __('ui.terms.s8_title') }}</h2>
                <p>{{ __('ui.terms.s8_body') }}</p>
            </div>

            <div class="terms-contact-box">
                <h3>{{ __('ui.terms.contact_title') }}</h3>
                <p>{{ __('ui.terms.contact_body') }}</p>
                <ul>
                    <li>PT Sazara Global Trade</li>
                    <li>Medan, North Sumatra, Indonesia</li>
                    <li><a href="mailto:contact@sazaraglobal.com">contact@sazaraglobal.com</a></li>
                    <li>www.sazaraglobal.com</li>
                </ul>
            </div>

        </div>

        {{-- Back Link --}}
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
