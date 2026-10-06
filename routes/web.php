<?php

use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\PageSectionController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Middleware\AdminAuth;
use App\Models\Article;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

/* ================================================================
    PUBLIC ROUTES
   ================================================================ */

/* Language switcher */
Route::get('/lang/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['en', 'id'], true), 404);
    session(['locale' => $locale]);
    return back();
})->name('lang.switch');

Route::get('/', fn () => view('pages.home', [
    'featured' => Product::active()->featured()->ordered()->get(),
]))->name('home');

Route::get('/products', fn () => view('pages.products', [
    'products' => Product::active()->ordered()->get(),
]))->name('products.index');

Route::get('/products/{slug}', fn (string $slug) => view('pages.products-show', [
    'product' => Product::active()->where('slug', $slug)->firstOrFail(),
]))->name('products.show');

Route::get('/articles', fn () => view('pages.articles', [
    'articles' => Article::published()->latest('published_at')->get(),
]))->name('articles.index');

Route::get('/articles/{slug}', fn (string $slug) => view('pages.articles-show', [
    'article' => Article::published()->where('slug', $slug)->firstOrFail(),
]))->name('articles.show');

Route::view('/about-us', 'pages.about')->name('about');
Route::view('/contact-us', 'pages.contact')->name('contact');

/* ================================================================
    ADMIN AUTH — Hidden login URL
   ================================================================ */
Route::get('/login-sazara', [AuthController::class, 'showLogin'])->name('admin.login');
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

    // Media
    Route::get('/media',          [MediaController::class, 'index'])->name('admin.media.index');
    Route::post('/media',         [MediaController::class, 'store'])->name('admin.media.store');
    Route::delete('/media/{medium}', [MediaController::class, 'destroy'])->name('admin.media.destroy');

    // Pages / Content
    Route::get('/pages',              [PageSectionController::class, 'index'])->name('admin.pages.index');
    Route::get('/pages/{page}',       [PageSectionController::class, 'edit'])->name('admin.pages.edit');
    Route::put('/pages/{page}',       [PageSectionController::class, 'update'])->name('admin.pages.update');

    // Settings
    Route::get('/settings',       [SettingsController::class, 'index'])->name('admin.settings.index');
    Route::put('/settings',       [SettingsController::class, 'update'])->name('admin.settings.update');
    Route::put('/settings/site',  [SettingsController::class, 'updateSite'])->name('admin.settings.site');
});