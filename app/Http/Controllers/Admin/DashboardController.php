<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Media;
use App\Models\PageSection;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'products'           => Product::count(),
            'articles'           => Article::count(),
            'media'              => Media::count(),
            'pages'              => 5,
            'active_products'    => Product::active()->count(),
            'published_articles' => Article::published()->count(),
            'wa_display'         => PageSection::getSetting('whatsapp_number', config('sazara.whatsapp_display', '+62 812-6040-7208')),
            'wa_clean'           => PageSection::getCleanWhatsApp(),
            'wa_configured'      => true,
        ];

        $recent_products = Product::latest()->take(5)->get();
        $recent_articles = Article::latest()->take(5)->get();
        $recent_media    = Media::latest()->take(6)->get();

        return view('admin.dashboard', compact('stats', 'recent_products', 'recent_articles', 'recent_media'));
    }
}
