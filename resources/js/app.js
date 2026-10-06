/* =====================================================
   SAZARA GLOBAL — Frontend Scripts & Interactions
   Theme Switcher, Transparent Scrolled Navbar,
   Smooth Scroll, Mobile Navigation, WhatsApp & Animations
   ===================================================== */

const cleanPhone = (num) => {
    let clean = (num || '').replace(/[^0-9]/g, '');
    if (clean.startsWith('0')) clean = '62' + clean.slice(1);
    return clean || '6281260407208';
};

const isIndonesian = () => document.documentElement.lang === 'id';

const getWaNumber = () => cleanPhone(window.sazaraConfig?.waNumber || document.querySelector('meta[name="wa-number"]')?.content || '6281260407208');
const getWaDefault = () => {
    if (window.sazaraConfig?.waDefault && window.sazaraConfig.waDefault !== 'Custom Inquiry') {
        return window.sazaraConfig.waDefault;
    }
    const meta = document.querySelector('meta[name="wa-default"]')?.content;
    if (meta && meta !== 'Custom Inquiry') {
        return meta;
    }
    return isIndonesian()
        ? 'Halo Sazara Global, saya ingin mengetahui lebih lanjut tentang layanan ekspor komoditas Anda.'
        : 'Hello Sazara Global, I would like to know more about your commodity export services.';
};
const waURL = (msg) => `https://wa.me/${getWaNumber()}?text=${encodeURIComponent(msg || getWaDefault())}`;

document.addEventListener('DOMContentLoaded', () => {
    /* ==== 1. Dark / Light Theme Switcher ==== */
    const themeToggle = document.getElementById('themeToggle');

    const getCurrentTheme = () => {
        return document.documentElement.getAttribute('data-theme') || 'dark';
    };

    const setTheme = (theme) => {
        document.documentElement.setAttribute('data-theme', theme);
        try {
            localStorage.setItem('sazara_theme', theme);
        } catch (e) {
            // LocalStorage fallback
        }
    };

    themeToggle?.addEventListener('click', () => {
        const nextTheme = getCurrentTheme() === 'dark' ? 'light' : 'dark';
        setTheme(nextTheme);
    });

    /* ==== 2. Transparent Navbar on Scroll ==== */
    const header = document.getElementById('siteHeader');
    const updateHeaderScroll = () => {
        if (!header) return;
        if (window.scrollY > 24) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    };
    window.addEventListener('scroll', updateHeaderScroll, { passive: true });
    updateHeaderScroll();

    /* ==== 3. Get Offer Buttons on Cards ==== */
    document.querySelectorAll('[data-wa]').forEach((el) => {
        if (el.closest('.wa-widget')) return;
        const target = el.dataset.wa;
        let msg = getWaDefault();

        if (target && target !== 'default') {
            const lower = target.toLowerCase();
            if (lower.includes('other') || lower.includes('lainnya') || lower.includes('custom')) {
                msg = isIndonesian()
                    ? 'Halo Sazara Global, saya memiliki permintaan khusus (Custom Inquiry). Mohon informasi ketersediaan dan penawaran terbaik.'
                    : 'Hello Sazara Global, I have a custom commodity inquiry. Please let me know the availability and best offer.';
            } else {
                msg = isIndonesian()
                    ? `Halo Sazara Global, saya tertarik dengan ${target}. Mohon kirimkan penawaran terbaik.`
                    : `Hello Sazara Global, I am interested in ${target}. Please send me your best offer.`;
            }
        }

        el.href = waURL(msg);
        el.target = '_blank';
        el.rel = 'noopener';
    });

    /* ==== 4. Mobile Navigation & Accessibility ==== */
    const toggle = document.getElementById('navToggle');
    const nav = document.getElementById('mainNav');

    const closeNav = () => {
        if (!nav || !toggle) return;
        nav.classList.remove('open');
        toggle.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');
    };

    const openNav = () => {
        if (!nav || !toggle) return;
        nav.classList.add('open');
        toggle.classList.add('open');
        toggle.setAttribute('aria-expanded', 'true');
    };

    toggle?.addEventListener('click', (e) => {
        e.stopPropagation();
        nav?.classList.contains('open') ? closeNav() : openNav();
    });

    // Close mobile nav when clicking outside or pressing Escape
    document.addEventListener('click', (e) => {
        if (header && !header.contains(e.target)) {
            closeNav();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeNav();
        }
    });

    // Close mobile nav when clicking any nav link
    nav?.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            closeNav();
        });
    });

    /* ==== 5. Global Smooth Scroll for Anchor Links ==== */
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (!href || href === '#') return;

            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                closeNav();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                if (window.history && window.history.pushState) {
                    window.history.pushState(null, null, href);
                }
            }
        });
    });

    /* ==== 6. Contact Form Handling ==== */
    const contactForm = document.getElementById('contactForm');
    const contactStatus = document.getElementById('contactStatus');

    if (contactForm) {
        contactForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const name = contactForm.querySelector('#name')?.value.trim() || '';
            const email = contactForm.querySelector('#email')?.value.trim() || '';
            const company = contactForm.querySelector('#company')?.value.trim() || '';
            const message = contactForm.querySelector('#message')?.value.trim() || '';

            const isId = document.documentElement.lang === 'id';
            const lines = [
                isId ? 'Halo Sazara Global, saya ingin bertanya.' : 'Hello Sazara Global, I would like to inquire.',
                `${isId ? 'Nama' : 'Name'}: ${name}`,
                `Email: ${email}`,
            ];
            if (company) {
                lines.push(`${isId ? 'Perusahaan' : 'Company'}: ${company}`);
            }
            lines.push(`${isId ? 'Pesan' : 'Message'}: ${message}`);

            if (contactStatus) {
                contactStatus.className = 'form-status ok';
                contactStatus.style.display = 'block';
                contactStatus.textContent = isId
                    ? 'Terima kasih. Pesan Anda sedang dialihkan ke WhatsApp untuk respon langsung...'
                    : 'Thank you. Redirecting your message to WhatsApp for immediate assistance...';
            }

            setTimeout(() => {
                window.open(waURL(lines.join('\n')), '_blank', 'noopener');
            }, 600);
        });
    }

    /* ==== 7. WhatsApp Floating Chat Widget ==== */
    const widget   = document.getElementById('waWidget');
    const panel    = document.getElementById('waPanel');
    const btnOpen  = document.getElementById('waToggle');
    const btnClose = document.getElementById('waClose');
    const body     = document.getElementById('waBody');
    const form     = document.getElementById('waForm');
    const input    = document.getElementById('waInput');
    const btnSend  = document.getElementById('waSend');
    const icMic    = btnSend?.querySelector('.ic-mic');
    const icSend   = btnSend?.querySelector('.ic-send');

    const nowTime = () => new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    const CHECK = '<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M1.7 12.6l4.4 4.4L15 8.1"/><path d="M9.6 12.6l4.4 4.4L22.9 8.1"/></svg>';

    document.querySelectorAll('.wa-meta[data-now]').forEach((el) => {
        el.textContent = nowTime();
    });

    const openPanel = () => {
        panel?.classList.add('open');
        setTimeout(() => input?.focus(), 200);
    };

    const closePanel = () => {
        panel?.classList.remove('open');
    };

    btnOpen?.addEventListener('click', () => {
        panel?.classList.contains('open') ? closePanel() : openPanel();
    });

    btnClose?.addEventListener('click', closePanel);

    document.addEventListener('click', (e) => {
        if (!widget?.contains(e.target)) closePanel();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && panel?.classList.contains('open')) {
            closePanel();
        }
    });

    input?.addEventListener('input', () => {
        const has = input.value.trim().length > 0;
        if (icMic)  icMic.hidden  = has;
        if (icSend) icSend.hidden = !has;
        btnSend?.classList.toggle('send', has);
    });

    const addOut = (text) => {
        if (!body) return;
        const wrap = document.createElement('div');
        wrap.className = 'wa-msg out';
        wrap.innerHTML = '<div class="wa-bubble"><p></p><span class="wa-meta">' + nowTime() + CHECK + '</span></div>';
        wrap.querySelector('p').textContent = text;
        body.appendChild(wrap);
        body.scrollTop = body.scrollHeight;
    };

    form?.addEventListener('submit', (e) => {
        e.preventDefault();
        const text = input?.value.trim();
        if (!text) return;
        addOut(text);
        if (input) input.value = '';
        input?.dispatchEvent(new Event('input'));
        setTimeout(() => window.open(waURL(text), '_blank', 'noopener'), 400);
    });

    /* ==== 8. Smart Auto-Animator (Intersection Observer) ==== */
    if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        // A. Hero Animations
        document.querySelectorAll('.hero .eyebrow').forEach((el) => el.classList.add('hero-anim'));
        document.querySelectorAll('.hero h1').forEach((el) => el.classList.add('hero-anim', 'hero-anim-delay-1'));
        document.querySelectorAll('.hero .lead').forEach((el) => el.classList.add('hero-anim', 'hero-anim-delay-2'));
        document.querySelectorAll('.hero .btn').forEach((el) => el.classList.add('hero-anim', 'hero-anim-delay-3'));

        // B. Scroll Targets
        const targets = document.querySelectorAll(
            '.section-head, .card, .flow-card, .team-card, .info-card, .article-body p, .cta-strip h2, .cta-strip p, .cta-strip .btn, .product-showcase-img, .article-detail-thumb, .spec-box'
        );

        targets.forEach((el) => {
            el.classList.add('anim-target');

            if (el.classList.contains('card') || el.classList.contains('flow-card') || el.classList.contains('team-card') || el.classList.contains('info-card') || el.classList.contains('spec-box')) {
                el.classList.add('anim-pop-in');
            } else if (el.tagName === 'P' && el.closest('.article-body')) {
                el.classList.add('anim-fade-in');
            } else {
                el.classList.add('anim-fade-up');
            }

            const parent = el.parentElement;
            if (parent && (parent.classList.contains('grid-3') || parent.classList.contains('grid-4') || parent.classList.contains('flow-grid') || parent.classList.contains('vision-mission-grid'))) {
                const siblings = Array.from(parent.children);
                const idx = siblings.indexOf(el);
                el.classList.add(`anim-stagger-${(idx % 4) + 1}`);
            }
        });

        // C. Observe elements
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.08, rootMargin: '0px 0px -30px 0px' });

            targets.forEach((el) => observer.observe(el));
        } else {
            targets.forEach((el) => el.classList.add('is-visible'));
        }
    }
});