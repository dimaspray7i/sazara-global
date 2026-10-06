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

        $query = Media::public()->orderBy('sort_order', 'asc')->latest('id');

        if (filled($category) && $category !== 'all') {
            $query->where('category', $category);
        }

        $galleryItems = $query->paginate(12)->withQueryString();

        $categories = [
            'all'         => __('ui.gallery.all'),
            'commodities' => __('ui.gallery.commodities'),
            'insights'    => __('ui.gallery.insights'),
            'company'     => __('ui.gallery.company'),
        ];

        $title = PageSection::get('gallery', 'header', 'title', __('ui.gallery.title'));
        $sub   = PageSection::get('gallery', 'header', 'subtitle', __('ui.gallery.sub'));

        return view('pages.gallery', compact('galleryItems', 'categories', 'category', 'title', 'sub'));
    }
}
