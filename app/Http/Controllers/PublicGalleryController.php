<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\GalleryCategory;
use App\Models\PageSection;
use Illuminate\Http\Request;

class PublicGalleryController extends Controller
{
    public function index(Request $request)
    {
        $categorySlug = $request->query('category');

        $query = Gallery::with(['coverMedia', 'media'])
            ->published()
            ->orderBy('sort_order', 'asc')
            ->latest('id');

        if (filled($categorySlug) && $categorySlug !== 'all') {
            $query->where('category_slug', $categorySlug);
        }

        $galleries = $query->paginate(12)->withQueryString();

        // Dynamic categories from DB
        $categories = GalleryCategory::active()->get();

        $title = PageSection::get('gallery', 'header', 'title', __('ui.gallery.title'));
        $sub   = PageSection::get('gallery', 'header', 'subtitle', __('ui.gallery.sub'));

        return view('pages.gallery', compact('galleries', 'categories', 'categorySlug', 'title', 'sub'));
    }

    /** Detail page / media JSON for lightbox (AJAX or direct) */
    public function show(Request $request, string $locale, $gallery)
    {
        if (! ($gallery instanceof Gallery)) {
            $gallery = Gallery::findOrFail($gallery);
        }

        // Only show published galleries publicly
        abort_unless($gallery->isPublished(), 404);

        $gallery->load('media');

        $locale = app()->getLocale();
        $data = [
            'id'          => $gallery->id,
            'title'       => $gallery->tr('title'),
            'description' => $gallery->tr('description'),
            'type'        => $gallery->type,
            'category'    => $gallery->category?->label(),
            'media'       => $gallery->media->map(function ($m) {
                return [
                    'id'        => $m->id,
                    'url'       => $m->url(),
                    'mime_type' => $m->mime_type,
                    'title'     => $m->tr('title'),
                    'caption'   => $m->tr('caption'),
                    'is_video'  => str_starts_with($m->mime_type, 'video/'),
                ];
            }),
        ];

        if ($request->wantsJson() || $request->ajax() || $request->boolean('json') || $request->header('X-Requested-With') === 'XMLHttpRequest' || $request->query('format') === 'json') {
            return response()->json($data);
        }

        // If accessed directly without JS, redirect to gallery index with hash
        return redirect()->route('gallery.index', ['locale' => $locale, 'open' => $gallery->id]);
    }
}
