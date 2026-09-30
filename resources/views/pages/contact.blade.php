@extends('layouts.app')
@section('title', __('ui.nav.contact') . ' — Sazara Global')
@section('content')
<section class="section section-alt">
    <div class="container">
        <div class="section-head"><h2>{{ __('ui.contact.title') }}</h2><div class="bar"></div><p>{{ __('ui.contact.sub') }}</p></div>
        <div class="contact-grid">
            <div class="info-card contact-info-card">
                <h3>PT Sazara Global Trade</h3>
                <div class="contact-item">
                    <span class="contact-icon">📍</span>
                    <p>Medan, North Sumatra, Indonesia</p>
                </div>
                <div class="contact-item">
                    <span class="contact-icon">✉️</span>
                    <p><a href="mailto:contact@sazaraglobal.com">contact@sazaraglobal.com</a></p>
                </div>
                <div class="contact-item">
                    <span class="contact-icon">🌐</span>
                    <p><a href="https://www.sazaraglobal.com" target="_blank" rel="noopener">www.sazaraglobal.com</a></p>
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