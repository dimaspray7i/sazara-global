<?php

use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\PageSectionController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\PublicGalleryController;
use App\Http\Controllers\PublicSearchController;
use App\Http\Middleware\AdminAuth;
use App\Models\Article;
use App\Models\Media;
use App\Models\Product;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

/* ================================================================
    SEO ROUTES — sitemap & robots
   ================================================================ */

Route::get('/robots.txt', function () {
    $appUrl = rtrim(config('app.url'), '/');
    $sitemapUrl = rtrim(env('APP_PRODUCTION_URL', 'https://sazaraglobal.com'), '/') . '/sitemap.xml';
    $content = "User-agent: *\n"
        . "Allow: /\n"
        . "Disallow: /admin\n"
        . "Disallow: /admin/\n"
        . "Disallow: /login-sazara\n"
        . "Disallow: /admin-logout\n"
        . "Disallow: /lang/\n\n"
        . "Sitemap: {$sitemapUrl}\n";
    return Response::make($content, 200, ['Content-Type' => 'text/plain']);
})->name('robots');

Route::get('/sitemap.xml', function () {
    $baseUrl = rtrim(env('APP_PRODUCTION_URL', 'https://sazaraglobal.com'), '/');
    $locales = ['id', 'en'];
    $defaultLocale = 'en';

    $staticPages = [
        ['id' => 'home',    'id_path' => '',        'en_path' => '',        'priority' => '1.0'],
        ['id' => 'about',   'id_path' => '/about',  'en_path' => '/about',  'priority' => '0.8'],
        ['id' => 'products','id_path' => '/products','en_path' => '/products','priority' => '0.9'],
        ['id' => 'articles','id_path' => '/articles','en_path' => '/articles','priority' => '0.8'],
        ['id' => 'gallery', 'id_path' => '/gallery', 'en_path' => '/gallery', 'priority' => '0.7'],
        ['id' => 'contact', 'id_path' => '/contact', 'en_path' => '/contact', 'priority' => '0.6'],
    ];

    $products = Product::active()->ordered()->get();
    $articles = Article::published()->latest('published_at')->get();

    $now = now()->format('Y-m-d');

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
    $xml .= '        xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

    // Static pages
    foreach ($staticPages as $page) {
        foreach ($locales as $locale) {
            $path = $page["{$locale}_path"];
            $url = "{$baseUrl}/{$locale}{$path}";
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$url}</loc>\n";
            $xml .= "    <lastmod>{$now}</lastmod>\n";
            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= "    <priority>{$page['priority']}</priority>\n";
            foreach ($locales as $altLocale) {
                $altPath = $page["{$altLocale}_path"];
                $altUrl = "{$baseUrl}/{$altLocale}{$altPath}";
                $xml .= "    <xhtml:link rel=\"alternate\" hreflang=\"{$altLocale}\" href=\"{$altUrl}\"/>\n";
            }
            $xml .= "    <xhtml:link rel=\"alternate\" hreflang=\"x-default\" href=\"{$baseUrl}/en{$path}\"/>\n";
            $xml .= "  </url>\n";
        }
    }

    // Products
    foreach ($products as $p) {
        foreach ($locales as $locale) {
            $url = "{$baseUrl}/{$locale}/products/{$p->slug}";
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$url}</loc>\n";
            $xml .= "    <lastmod>{$p->updated_at->format('Y-m-d')}</lastmod>\n";
            $xml .= "    <changefreq>monthly</changefreq>\n";
            $xml .= "    <priority>0.8</priority>\n";
            foreach ($locales as $altLocale) {
                $xml .= "    <xhtml:link rel=\"alternate\" hreflang=\"{$altLocale}\" href=\"{$baseUrl}/{$altLocale}/products/{$p->slug}\"/>\n";
            }
            $xml .= "    <xhtml:link rel=\"alternate\" hreflang=\"x-default\" href=\"{$baseUrl}/en/products/{$p->slug}\"/>\n";
            $xml .= "  </url>\n";
        }
    }

    // Articles
    foreach ($articles as $a) {
        foreach ($locales as $locale) {
            $url = "{$baseUrl}/{$locale}/articles/{$a->slug}";
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$url}</loc>\n";
            $xml .= "    <lastmod>{$a->updated_at->format('Y-m-d')}</lastmod>\n";
            $xml .= "    <changefreq>monthly</changefreq>\n";
            $xml .= "    <priority>0.7</priority>\n";
            foreach ($locales as $altLocale) {
                $xml .= "    <xhtml:link rel=\"alternate\" hreflang=\"{$altLocale}\" href=\"{$baseUrl}/{$altLocale}/articles/{$a->slug}\"/>\n";
            }
            $xml .= "    <xhtml:link rel=\"alternate\" hreflang=\"x-default\" href=\"{$baseUrl}/en/articles/{$a->slug}\"/>\n";
            $xml .= "  </url>\n";
        }
    }

    $xml .= '</urlset>';
    return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
})->name('sitemap');

/* ================================================================
    PUBLIC ROUTES — Language-aware
   ================================================================ */

/* Language switcher — saves locale and redirects back */
Route::get('/lang/{locale}', function (string $locale) {
    $locale = strtolower($locale);
    $supported = \App\Models\Language::getSupportedCodes();

    // If not found in cached supported codes, do a direct DB check in case cache is stale
    if (! in_array($locale, $supported, true)) {
        if (\App\Models\Language::where('is_active', true)->where('code', $locale)->exists()) {
            \Illuminate\Support\Facades\Cache::forget('sazara_supported_lang_codes');
            $supported[] = $locale;
        }
    }

    abort_unless(in_array($locale, $supported, true), 404);

    session(['locale' => $locale]);
    // Try to redirect to same page with new locale prefix
    $referer = request()->headers->get('referer', '/');
    $parsedPath = parse_url($referer, PHP_URL_PATH) ?? '/';
    // Strip existing /[a-z]{2,3} prefix from referer if present
    $stripped = preg_replace('#^/([a-z]{2,3})(/|$)#', '/', $parsedPath);
    return redirect("/{$locale}" . ($stripped === '/' ? '' : $stripped));
})->name('lang.switch');

/* Root redirect — sends to locale-prefixed URL based on session */
Route::get('/', function () {
    $locale = session('locale', 'en');
    $supported = \App\Models\Language::getSupportedCodes();
    if (! in_array($locale, $supported, true)) $locale = 'en';
    return redirect("/{$locale}", 301);
})->name('home.redirect');

/* ====== Locale-prefixed public routes ====== */
Route::prefix('{locale}')
    ->where(['locale' => '[a-z]{2,3}'])
    ->group(function () {


    Route::get('/', fn () => view('pages.home', [
        'featured' => Product::active()->featured()->ordered()->get(),
    ]))->name('home');

    Route::get('/products', fn () => view('pages.products', [
        'products' => Product::active()->ordered()->get(),
    ]))->name('products.index');

    Route::get('/products/{slug}', fn (string $locale, string $slug) => view('pages.products-show', [
        'product' => Product::active()->where('slug', $slug)->firstOrFail(),
    ]))->name('products.show');

    Route::get('/articles', fn () => view('pages.articles', [
        'articles' => Article::published()->latest('published_at')->get(),
    ]))->name('articles.index');

    Route::get('/articles/{slug}', fn (string $locale, string $slug) => view('pages.articles-show', [
        'article' => Article::published()->where('slug', $slug)->firstOrFail(),
    ]))->name('articles.show');

    Route::get('/about',   fn () => view('pages.about')  )->name('about');
    Route::get('/contact', fn () => view('pages.contact'))->name('contact');

    Route::get('/gallery', [PublicGalleryController::class, 'index'])->name('gallery.index');

    Route::get('/search', [PublicSearchController::class, 'search'])->name('search');
    Route::get('/api/search', [PublicSearchController::class, 'search'])->name('search.ajax');
});

/* ====== Legacy compatibility redirects (no locale prefix) ====== */
Route::get('/products',         fn () => redirect()->route('products.index', ['locale' => session('locale', 'en')], 301))->name('products.index.legacy');
Route::get('/products/{slug}',  fn ($slug) => redirect()->route('products.show', ['locale' => session('locale', 'en'), 'slug' => $slug], 301))->name('products.show.legacy');
Route::get('/articles',         fn () => redirect()->route('articles.index', ['locale' => session('locale', 'en')], 301))->name('articles.index.legacy');
Route::get('/articles/{slug}',  fn ($slug) => redirect()->route('articles.show', ['locale' => session('locale', 'en'), 'slug' => $slug], 301))->name('articles.show.legacy');
Route::get('/about-us',         fn () => redirect()->route('about', ['locale' => session('locale', 'en')], 301))->name('about.legacy');
Route::get('/contact-us',       fn () => redirect()->route('contact', ['locale' => session('locale', 'en')], 301))->name('contact.legacy');
Route::get('/gallery',          fn () => redirect()->route('gallery.index', ['locale' => session('locale', 'en')], 301))->name('gallery.legacy');
Route::get('/search',           fn () => redirect()->route('search', ['locale' => session('locale', 'en')], 301))->name('search.legacy');

/* ================================================================
    ADMIN AUTH — Hidden login URL
   ================================================================ */
Route::get('/login-sazara',  [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/login-sazara', [AuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin-logout', [AuthController::class, 'logout'])->name('admin.logout');

/* ================================================================
    ADMIN PANEL — All routes protected by AdminAuth middleware
   ================================================================ */
Route::prefix('admin')->middleware(AdminAuth::class)->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Products
    Route::get('/products',               [AdminProductController::class, 'index'])->name('admin.products.index');
    Route::get('/products/create',         [AdminProductController::class, 'create'])->name('admin.products.create');
    Route::post('/products',               [AdminProductController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{product}',      [AdminProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{product}',   [AdminProductController::class, 'destroy'])->name('admin.products.destroy');
    Route::post('/products/{product}/toggle', [AdminProductController::class, 'toggle'])->name('admin.products.toggle');

    // Articles
    Route::get('/articles',               [AdminArticleController::class, 'index'])->name('admin.articles.index');
    Route::get('/articles/create',         [AdminArticleController::class, 'create'])->name('admin.articles.create');
    Route::post('/articles',               [AdminArticleController::class, 'store'])->name('admin.articles.store');
    Route::get('/articles/{article}/edit', [AdminArticleController::class, 'edit'])->name('admin.articles.edit');
    Route::put('/articles/{article}',      [AdminArticleController::class, 'update'])->name('admin.articles.update');
    Route::delete('/articles/{article}',   [AdminArticleController::class, 'destroy'])->name('admin.articles.destroy');
    Route::post('/articles/{article}/toggle', [AdminArticleController::class, 'toggle'])->name('admin.articles.toggle');

    // Media / Gallery
    Route::get('/media',                    [MediaController::class, 'index'])->name('admin.media.index');
    Route::post('/media',                   [MediaController::class, 'store'])->name('admin.media.store');
    Route::put('/media/{medium}',           [MediaController::class, 'update'])->name('admin.media.update');
    Route::post('/media/{medium}/toggle',   [MediaController::class, 'toggle'])->name('admin.media.toggle');
    Route::delete('/media/{medium}',        [MediaController::class, 'destroy'])->name('admin.media.destroy');

    // Pages / Content
    Route::get('/pages',        [PageSectionController::class, 'index'])->name('admin.pages.index');
    Route::get('/pages/{page}', [PageSectionController::class, 'edit'])->name('admin.pages.edit');
    Route::put('/pages/{page}', [PageSectionController::class, 'update'])->name('admin.pages.update');

    // Settings
    Route::get('/settings',      [SettingsController::class, 'index'])->name('admin.settings.index');
    Route::put('/settings',      [SettingsController::class, 'update'])->name('admin.settings.update');
    Route::put('/settings/site', [SettingsController::class, 'updateSite'])->name('admin.settings.site');

    // Dynamic Translations & Languages
    Route::get('/translations',                       [\App\Http\Controllers\Admin\TranslationController::class, 'index'])->name('admin.translations.index');
    Route::post('/languages',                         [\App\Http\Controllers\Admin\TranslationController::class, 'storeLanguage'])->name('admin.languages.store');
    Route::post('/languages/{language}/toggle',       [\App\Http\Controllers\Admin\TranslationController::class, 'toggleLanguage'])->name('admin.languages.toggle');
    Route::put('/translations/{translation}',         [\App\Http\Controllers\Admin\TranslationController::class, 'updateTranslation'])->name('admin.translations.update');
    Route::post('/translations/auto-translate',       [\App\Http\Controllers\Admin\TranslationController::class, 'autoTranslateMissing'])->name('admin.translations.auto');
});