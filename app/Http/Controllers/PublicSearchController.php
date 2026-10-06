<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Media;
use App\Models\Product;
use Illuminate\Http\Request;

class PublicSearchController extends Controller
{
    public function search(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $isAjax = $request->ajax() || $request->wantsJson() || $request->boolean('ajax');

        if (mb_strlen($q) < 2) {
            if ($isAjax) {
                return response()->json(['results' => [], 'total' => 0]);
            }
            return view('pages.search', [
                'query'   => $q,
                'results' => collect([]),
                'total'   => 0,
            ]);
        }

        $results = collect();
        $isId = app()->getLocale() === 'id';
        $prefix = $isId ? '/id' : '/en';

        // 1. Search Products
        $products = Product::active()
            ->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('name_id', 'like', "%{$q}%")
                  ->orWhere('description', 'like', "%{$q}%")
                  ->orWhere('description_id', 'like', "%{$q}%");
            })
            ->take($isAjax ? 4 : 10)
            ->get();

        foreach ($products as $p) {
            $results->push([
                'type'        => __('ui.search.type_product'),
                'type_key'    => 'product',
                'title'       => $p->tr('name'),
                'description' => \Illuminate\Support\Str::limit(strip_tags($p->tr('description')), 120),
                'url'         => url("{$prefix}/products/{$p->slug}"),
                'thumbnail'   => $p->imageUrl(),
            ]);
        }

        // 2. Search Articles
        $articles = Article::published()
            ->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                  ->orWhere('title_id', 'like', "%{$q}%")
                  ->orWhere('body', 'like', "%{$q}%")
                  ->orWhere('body_id', 'like', "%{$q}%")
                  ->orWhere('excerpt', 'like', "%{$q}%");
            })
            ->take($isAjax ? 3 : 10)
            ->get();

        foreach ($articles as $a) {
            $results->push([
                'type'        => __('ui.search.type_article'),
                'type_key'    => 'article',
                'title'       => $a->tr('title'),
                'description' => \Illuminate\Support\Str::limit(strip_tags($a->tr('body')), 120),
                'url'         => url("{$prefix}/articles/{$a->slug}"),
                'thumbnail'   => $a->imageUrl(),
            ]);
        }

        // 3. Search Gallery
        $gallery = Media::public()
            ->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                  ->orWhere('title_id', 'like', "%{$q}%")
                  ->orWhere('caption', 'like', "%{$q}%")
                  ->orWhere('caption_id', 'like', "%{$q}%")
                  ->orWhere('original_name', 'like', "%{$q}%");
            })
            ->take($isAjax ? 3 : 8)
            ->get();

        foreach ($gallery as $g) {
            $results->push([
                'type'        => __('ui.gallery.title'),
                'type_key'    => 'gallery',
                'title'       => $g->tr('title'),
                'description' => \Illuminate\Support\Str::limit($g->tr('caption') ?: $g->alt, 100),
                'url'         => url("{$prefix}/gallery"),
                'thumbnail'   => $g->url(),
            ]);
        }

        // 4. Static Pages
        $pages = [
            [
                'title'       => __('ui.nav.about'),
                'description' => __('ui.about.heading'),
                'url'         => url("{$prefix}/about"),
                'keywords'    => 'about us tentang kami profil sazara perusahaan',
            ],
            [
                'title'       => __('ui.nav.contact'),
                'description' => __('ui.contact.title'),
                'url'         => url("{$prefix}/contact"),
                'keywords'    => 'contact us kontak hubungi alamat whatsapp email',
            ],
            [
                'title'       => __('ui.nav.product'),
                'description' => __('ui.products.title'),
                'url'         => url("{$prefix}/products"),
                'keywords'    => 'commodities produk komoditas ekspor indonesia',
            ],
            [
                'title'       => __('ui.gallery.title'),
                'description' => __('ui.gallery.sub'),
                'url'         => url("{$prefix}/gallery"),
                'keywords'    => 'gallery galeri foto dokumentasi komoditas',
            ],
        ];

        foreach ($pages as $p) {
            if (stripos($p['title'], $q) !== false || stripos($p['description'], $q) !== false || stripos($p['keywords'], $q) !== false) {
                $results->push([
                    'type'        => __('ui.search.type_page'),
                    'type_key'    => 'page',
                    'title'       => $p['title'],
                    'description' => $p['description'],
                    'url'         => $p['url'],
                    'thumbnail'   => null,
                ]);
            }
        }

        if ($isAjax) {
            return response()->json([
                'results' => $results->take(8)->values(),
                'total'   => $results->count(),
                'query'   => $q,
            ]);
        }

        return view('pages.search', [
            'query'   => $q,
            'results' => $results,
            'total'   => $results->count(),
        ]);
    }
}
