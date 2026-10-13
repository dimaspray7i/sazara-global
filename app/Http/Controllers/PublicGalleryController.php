<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\PageSection;
use Illuminate\Http\Request;

class PublicGalleryController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('category');

        // Only show media explicitly marked as gallery items (is_gallery=true AND is_public=true)
        $query = Media::gallery()->orderBy('sort_order', 'asc')->latest('id');

        if (filled($category) && $category !== 'all') {
            $query->where('category', $category);
        }

        $galleryItems = $query->paginate(12)->withQueryString();

        // Gallery-specific activity categories
        $categories = [
            'all'        => __('ui.gallery.all'),
            'logistics'  => __('ui.gallery.logistics'),
            'plantation' => __('ui.gallery.plantation'),
            'harvesting' => __('ui.gallery.harvesting'),
            'packaging'  => __('ui.gallery.packaging'),
            'export'     => __('ui.gallery.export'),
            'company'    => __('ui.gallery.company'),
        ];

        $title = PageSection::get('gallery', 'header', 'title', __('ui.gallery.title'));
        $sub   = PageSection::get('gallery', 'header', 'subtitle', __('ui.gallery.sub'));

        return view('pages.gallery', compact('galleryItems', 'categories', 'category', 'title', 'sub'));
    }
}
