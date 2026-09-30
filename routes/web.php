<?php

use App\Models\Article;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

/* Switcher bahasa EN | ID */
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