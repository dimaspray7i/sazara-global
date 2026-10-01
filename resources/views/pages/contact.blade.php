@extends('layouts.app')

@php
    use App\Models\PageSection;
    $contactTitle   = PageSection::get('contact', 'header', 'title', __('ui.contact.title'));
    $contactSub     = PageSection::get('contact', 'header', 'subtitle', __('ui.contact.sub'));
    $contactAddress = PageSection::get('contact', 'info', 'address', 'Medan, North Sumatra, Indonesia');
    $contactEmail   = PageSection::get('contact', 'info', 'email', 'contact@sazaraglobal.com');
    $contactWebsite = PageSection::get('contact', 'info', 'website', 'www.sazaraglobal.com');
@endphp

@section('title', __('ui.nav.contact') . ' — Sazara Global')
@section('content')
<section class="section section-alt">
    <div class="container">
        <div class="section-head"><h2>{{ $contactTitle }}</h2><div class="bar"></div><p>{{ $contactSub }}</p></div>
        <div class="contact-grid">
            <div class="info-card contact-info-card">
                <h3>PT Sazara Global Trade</h3>
                <div class="contact-item">
                    <span class="contact-icon">📍</span>
                    <p>{{ $contactAddress }}</p>
                </div>
                <div class="contact-item">
                    <span class="contact-icon">✉️</span>
                    <p><a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a></p>
                </div>
                <div class="contact-item">
                    <span class="contact-icon">🌐</span>
                    <p><a href="{{ str_starts_with($contactWebsite, 'http') ? $contactWebsite : 'https://' . $contactWebsite }}" target="_blank" rel="noopener">{{ $contactWebsite }}</a></p>
                </div>
                <div class="contact-cta-wrap">
                    <a class="btn btn-secondary" data-wa="default" href="#">{{ __('ui.common.chat_wa') }}</a>
                </div>
            </div>
            <form class="contact-form" id="contactForm" action="#" method="POST">
                @csrf
                <div id="contactStatus" class="form-status" style="display:none"></div>
                <div class="field">
                    <label for="name">{{ __('ui.contact.name') }} <span class="required">*</span></label>
                    <input id="name" type="text" name="name" required autocomplete="name" placeholder="{{ __('ui.contact.name') }}">
                </div>
                <div class="field">
                    <label for="email">{{ __('ui.contact.email') }} <span class="required">*</span></label>
                    <input id="email" type="email" name="email" required autocomplete="email" placeholder="example@domain.com">
                </div>
                <div class="field">
                    <label for="company">{{ __('ui.contact.company') }}</label>
                    <input id="company" type="text" name="company" autocomplete="organization" placeholder="{{ __('ui.contact.company') }}">
                </div>
                <div class="field">
                    <label for="message">{{ __('ui.contact.message') }} <span class="required">*</span></label>
                    <textarea id="message" name="message" rows="5" required placeholder="{{ __('ui.contact.message') }}..."></textarea>
                </div>
                <button class="btn btn-primary" type="submit">{{ __('ui.common.send') }}</button>
            </form>
        </div>
    </div>
</section>
@endsection