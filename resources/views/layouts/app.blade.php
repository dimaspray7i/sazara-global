@php
$locale = app()->getLocale();
$prodUrl = rtrim(env('APP_PRODUCTION_URL','https://sazaraglobal.com'),'/');
$currentPath = '/' . trim(request()->path(), '/');
// Strip /en or /id prefix from path for the alternate URL builder
$basePath = preg_replace('#^/(en|id)(/?)#', '/', $currentPath);
if ($basePath === '') $basePath = '/';
$canonicalId = $prodUrl . '/id' . ($basePath === '/' ? '' : $basePath);
$canonicalEn = $prodUrl . '/en' . ($basePath === '/' ? '' : $basePath);
$canonicalCurrent = $locale === 'id' ? $canonicalId : $canonicalEn;
$defaultSeoTitle = __('ui.seo.home_title');
$defaultSeoDesc  = __('ui.seo.home_desc');
$ogImage = $prodUrl . '/images/logo.jpg';
$htmlDir = ($currentLanguage && $currentLanguage->isRtl()) ? 'rtl' : 'ltr';
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $htmlDir }}">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- SEO: Title --}}
    <title>@yield('seo_title', $defaultSeoTitle)</title>
    <meta name="description" content="@yield('seo_desc', $defaultSeoDesc)">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ $canonicalCurrent }}">

    {{-- Hreflang --}}
    <link rel="alternate" hreflang="id" href="{{ $canonicalId }}">
    <link rel="alternate" hreflang="en" href="{{ $canonicalEn }}">
    <link rel="alternate" hreflang="x-default" href="{{ $canonicalEn }}">

    {{-- Favicon --}}
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpg') }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="PT Sazara Global Trade">
    <meta property="og:locale" content="{{ $locale === 'id' ? 'id_ID' : 'en_US' }}">
    <meta property="og:locale:alternate" content="{{ $locale === 'id' ? 'en_US' : 'id_ID' }}">
    <meta property="og:title" content="@yield('seo_title', $defaultSeoTitle)">
    <meta property="og:description" content="@yield('seo_desc', $defaultSeoDesc)">
    <meta property="og:url" content="{{ $canonicalCurrent }}">
    <meta property="og:image" content="@yield('og_image', $ogImage)">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('seo_title', $defaultSeoTitle)">
    <meta name="twitter:description" content="@yield('seo_desc', $defaultSeoDesc)">
    <meta name="twitter:image" content="@yield('og_image', $ogImage)">

    {{-- JSON-LD: Organization + WebSite --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@graph": [
            {
                "@@type": "Organization",
                "@@id": "{{ $prodUrl }}/#organization",
                "name": "PT Sazara Global Trade",
                "alternateName": "Sazara Global",
                "url": "{{ $prodUrl }}",
                "logo": {
                    "@@type": "ImageObject",
                    "url": "{{ $prodUrl }}/images/logo.jpg",
                    "width": 1254,
                    "height": 1254
                },
                "address": {
                    "@@type": "PostalAddress",
                    "addressLocality": "Medan",
                    "addressRegion": "North Sumatra",
                    "addressCountry": "ID"
                },
                "contactPoint": {
                    "@@type": "ContactPoint",
                    "contactType": "customer support",
                    "telephone": "+62-812-6040-7208",
                    "availableLanguage": ["Indonesian", "English"]
                },
                "sameAs": ["https://sazaraglobal.com"]
            },
            {
                "@@type": "WebSite",
                "@@id": "{{ $prodUrl }}/#website",
                "url": "{{ $prodUrl }}",
                "name": "Sazara Global",
                "description": "{{ $defaultSeoDesc }}",
                "publisher": { "@@id": "{{ $prodUrl }}/#organization" },
                "potentialAction": {
                    "@@type": "SearchAction",
                    "target": { "@@type": "EntryPoint", "urlTemplate": "{{ $prodUrl }}/{{ $locale }}/search?q={search_term_string}" },
                    "query-input": "required name=search_term_string"
                }
            }
            @yield('schema_extra')
        ]
    }
    </script>


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

    <script>
        (function() {
            var saved = localStorage.getItem('sazara_theme');
            if (saved === 'light') {
                document.documentElement.setAttribute('data-theme', 'light');
            } else {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
    </script>
    <meta name="wa-number" content="{{ $globalWaNumber ?? '6281260407208' }}">
    <meta name="wa-default" content="{{ $globalWaDefault ?? 'Hello Sazara Global, I would like to make an inquiry...' }}">
    <script>
        window.sazaraConfig = {
            waNumber: "{{ $globalWaNumber ?? '6281260407208' }}",
            waDisplay: "{{ $globalWaDisplay ?? '+62 812-6040-7208' }}",
            waDefault: "{{ addslashes($globalWaDefault ?? 'Hello Sazara Global, I would like to make an inquiry...') }}",
            locale: "{{ $locale }}",
            searchUrl: "{{ route('search', ['locale' => $locale]) }}"
        };
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
<header class="site-header" id="siteHeader">
    <div class="header-inner">
        <a class="brand-link" href="{{ route('home', ['locale' => app()->getLocale()]) }}" aria-label="PT Sazara Global Trade — Home">
            <img class="brand-logo" src="{{ asset('images/logo.jpg') }}" alt="PT Sazara Global Trade Logo" width="1254" height="1254">
        </a>
        <nav class="main-nav" id="mainNav" aria-label="Main Navigation">
            <ul>
                <li><a class="{{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home', ['locale' => app()->getLocale()]) }}">{{ __('ui.nav.home') }}</a></li>
                <li><a class="{{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index', ['locale' => app()->getLocale()]) }}">{{ __('ui.nav.product') }}</a></li>
                <li><a class="{{ request()->routeIs('articles.*') ? 'active' : '' }}" href="{{ route('articles.index', ['locale' => app()->getLocale()]) }}">{{ __('ui.nav.article') }}</a></li>
                <li><a class="{{ request()->routeIs('gallery.*') ? 'active' : '' }}" href="{{ route('gallery.index', ['locale' => app()->getLocale()]) }}">{{ __('ui.nav.gallery') }}</a></li>
                <li><a class="{{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about', ['locale' => app()->getLocale()]) }}">{{ __('ui.nav.about') }}</a></li>
                <li><a class="{{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact', ['locale' => app()->getLocale()]) }}">{{ __('ui.nav.contact') }}</a></li>
            </ul>
        </nav>
        <div class="header-tools">
            {{-- Search trigger --}}
            <button class="search-trigger" id="searchTrigger" type="button" aria-label="{{ __('ui.search.placeholder') }}" title="{{ __('ui.search.placeholder') }}">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </button>

            {{-- Language Dropdown (Dynamic & Searchable) --}}
            @php
                $currentLocale = app()->getLocale();
                $displayLang = $currentLanguage ?? \App\Models\Language::findByCode($currentLocale) ?? \App\Models\Language::findByCode('en');
                $langList = $activeLanguages ?? \App\Models\Language::getActive();
            @endphp
            <div class="lang-dropdown" id="langDropdown" aria-label="Language Selector">
                <button class="lang-trigger" id="langTrigger" type="button" aria-expanded="false" aria-haspopup="listbox">
                    <span class="lang-globe">🌐</span>
                    <span class="lang-flag">{{ $displayLang?->flag }}</span>
                    <span class="lang-code">{{ strtoupper($currentLocale) }}</span>
                    <svg class="lang-caret" viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="lang-menu" id="langMenu" role="listbox">
                    <div class="lang-search-box">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input type="text" id="langSearchInput" class="lang-search-input" placeholder="Search language..." autocomplete="off">
                    </div>
                    <div class="lang-list" id="langList">
                        @foreach($langList as $l)
                            <a class="lang-option{{ $currentLocale === $l->code ? ' active' : '' }}"
                               href="{{ route('lang.switch', $l->code) }}"
                               data-name="{{ strtolower($l->name) }}"
                               data-native="{{ strtolower($l->native_name) }}"
                               data-code="{{ strtolower($l->code) }}"
                               role="option" aria-selected="{{ $currentLocale === $l->code ? 'true' : 'false' }}">
                                <span class="lang-flag">{{ $l->flag }}</span>
                                <span class="lang-names">
                                    <strong class="lang-native">{{ $l->native_name }}</strong>
                                    <small class="lang-en-name">{{ $l->name }}</small>
                                </span>
                                @if($currentLocale === $l->code)
                                    <svg class="lang-check" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>


            <button class="theme-toggle" id="themeToggle" type="button" aria-label="Toggle Dark/Light Mode" title="Toggle theme">
                <svg class="theme-icon-sun" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="5"></circle>
                    <line x1="12" y1="1" x2="12" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="23"></line>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                    <line x1="1" y1="12" x2="3" y2="12"></line>
                    <line x1="21" y1="12" x2="23" y2="12"></line>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                </svg>
                <svg class="theme-icon-moon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                </svg>
            </button>
            <button class="nav-toggle" id="navToggle" aria-label="Open Navigation Menu" aria-expanded="false" aria-controls="mainNav">
                <span class="hamburger-bar"></span>
                <span class="hamburger-bar"></span>
                <span class="hamburger-bar"></span>
            </button>
        </div>
    </div>
</header>

{{-- Search Modal --}}
<div class="search-overlay" id="searchOverlay" role="dialog" aria-label="{{ __('ui.search.placeholder') }}" aria-modal="true" hidden>
    <div class="search-modal">
        <div class="search-modal-header">
            <form class="search-form" id="searchForm" action="{{ route('search', ['locale' => app()->getLocale()]) }}" method="GET">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="q" id="searchInput" class="search-input" placeholder="{{ __('ui.search.placeholder') }}" autocomplete="off" aria-label="{{ __('ui.search.placeholder') }}">
                <button type="button" class="search-close-btn" id="searchClose" aria-label="Close search">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </form>
        </div>
        <div class="search-results" id="searchResults"></div>
    </div>
</div>


<main id="mainContent">@yield('content')</main>

<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-brand">
            <h4>PT Sazara Global Trade</h4>
            <p>{{ __('ui.footer.tagline') }}<br><em>{{ __('ui.footer.tagline2') }}</em></p>
        </div>
        <div>
            <h4>{{ __('ui.footer.pages') }}</h4>
            <ul>
                <li><a href="{{ route('home', ['locale' => app()->getLocale()]) }}">{{ __('ui.nav.home') }}</a></li>
                <li><a href="{{ route('products.index', ['locale' => app()->getLocale()]) }}">{{ __('ui.nav.product') }}</a></li>
                <li><a href="{{ route('articles.index', ['locale' => app()->getLocale()]) }}">{{ __('ui.nav.article') }}</a></li>
                <li><a href="{{ route('gallery.index', ['locale' => app()->getLocale()]) }}">{{ __('ui.nav.gallery') }}</a></li>
                <li><a href="{{ route('about', ['locale' => app()->getLocale()]) }}">{{ __('ui.nav.about') }}</a></li>
                <li><a href="{{ route('contact', ['locale' => app()->getLocale()]) }}">{{ __('ui.nav.contact') }}</a></li>
            </ul>
        </div>

        <div>
            <h4>{{ __('ui.footer.commodities') }}</h4>
            <ul><li>Palm Broom</li><li>CPO</li><li>Coffee</li><li>Clove</li><li>Cinnamon</li><li>Vanilla</li></ul>
        </div>
        <div>
            <h4>{{ __('ui.footer.contact') }}</h4>
            <ul>
                <li>{{ $globalAddress ?? 'Medan, North Sumatra, Indonesia' }}</li>
                <li><a href="mailto:{{ $globalEmail ?? 'contact@sazaraglobal.com' }}">{{ $globalEmail ?? 'contact@sazaraglobal.com' }}</a></li>
                <li><a href="{{ $globalWaUrl }}" target="_blank" rel="noopener">WhatsApp: {{ $globalWaDisplay ?? '+62 812-6040-7208' }} ({{ $globalContactPerson ?? 'Afriansyah Munar' }})</a></li>
                <li>www.sazaraglobal.com</li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <span>© {{ date('Y') }} PT Sazara Global Trade. {{ __('ui.footer.rights') }}</span>
        <a href="{{ route('terms', ['locale' => app()->getLocale()]) }}" class="footer-terms-link">{{ __('ui.footer.terms') }}</a>
    </div>
</footer>

{{-- ==== WhatsApp Chat Widget ==== --}}
<div class="wa-widget" id="waWidget">
    <button class="wa-float" id="waToggle" aria-label="Chat on WhatsApp">
        <svg viewBox="0 0 24 24" width="28" height="28" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
    </button>

    <div class="wa-panel" id="waPanel" role="dialog" aria-label="WhatsApp chat">
        <div class="wa-header">
            <div class="wa-avatar"><img src="https://api.iconify.design/fa6-solid:ship.svg?color=%23062B55" alt="PT Sazara Global Trade"></div>
            <div class="wa-info">
                <h4>PT Sazara Global Trade</h4>
                <span class="wa-status">{{ $globalContactPerson ?? 'Afriansyah Munar' }} · Online</span>
            </div>
            <div class="wa-header-icons">
                <button type="button" class="wa-icon" title="Video call" aria-label="Video call"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/></svg></button>
                <button type="button" class="wa-icon" title="Call" aria-label="Call"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg></button>
                <button type="button" class="wa-icon" title="Search" aria-label="Search"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg></button>
                <button type="button" class="wa-icon" id="waClose" title="Close" aria-label="Close chat"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M19 6.41 17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>
            </div>
        </div>

        <div class="wa-body" id="waBody">
            <div class="wa-date-pill">TODAY</div>
            <div class="wa-encrypt">
                <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
                <span>Messages and calls are end-to-end encrypted. Only people in this chat can read, listen to, or share them.</span>
            </div>
            <div class="wa-msg in">
                <div class="wa-bubble">
                    <p>Hello! 👋 Welcome to PT Sazara Global Trade. How can we help you today? Feel free to ask about our commodities or request a quote.</p>
                    <span class="wa-meta" data-now></span>
                </div>
            </div>
        </div>

        <form class="wa-footer" id="waForm">
            <div class="wa-inputwrap">
                <button type="button" class="wa-icon" title="Attach" aria-label="Attach"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M16.5 6v11.5c0 2.21-1.79 4-4 4s-4-1.79-4-4V5c0-1.38 1.12-2.5 2.5-2.5s2.5 1.12 2.5 2.5v10.5c0 .55-.45 1-1 1s-1-.45-1-1V6H10v9.5c0 1.38 1.12 2.5 2.5 2.5s2.5-1.12 2.5-2.5V5c0-2.21-1.79-4-4-4S7 2.79 7 5v12.5c0 3.04 2.46 5.5 5.5 5.5s5.5-2.46 5.5-5.5V6h-1.5z"/></svg></button>
                <button type="button" class="wa-icon" title="Emoji" aria-label="Emoji"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/></svg></button>
                <input type="text" id="waInput" class="wa-input" placeholder="Type a message" autocomplete="off">
            </div>
            <button type="submit" class="wa-sendbtn" id="waSend" aria-label="Send">
                <svg class="ic-mic" viewBox="0 0 24 24" width="22" height="22" fill="currentColor"><path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3zm5.91-3c-.49 0-.9.36-.98.85C16.52 14.2 14.47 16 12 16s-4.52-1.8-4.93-4.15c-.08-.49-.49-.85-.98-.85-.61 0-1.09.54-1 1.14.49 3 2.89 5.35 5.91 5.78V20c0 .55.45 1 1 1s1-.45 1-1v-2.08c3.02-.43 5.42-2.78 5.91-5.78.1-.6-.39-1.14-1-1.14z"/></svg>
                <svg class="ic-send" viewBox="0 0 24 24" width="22" height="22" fill="currentColor" hidden><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
            </button>
        </form>
    </div>
</div>
</body>
</html>