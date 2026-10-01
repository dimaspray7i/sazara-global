<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\PageSection;
use Illuminate\Http\Request;

class PageSectionController extends Controller
{
    /**
     * Pages available for editing.
     * Each page has labeled sections with their editable fields.
     */
    private const PAGES = [
        'home' => [
            'label'    => 'Home',
            'sections' => [
                'hero'  => ['label' => 'Hero Section',   'fields' => ['eyebrow','title', 'title2', 'description', 'btn_text', 'btn_url']],
                'flow'  => ['label' => 'Business Flow',  'fields' => ['title', 'subtitle']],
                'why'   => ['label' => 'Why Partner',    'fields' => ['title']],
                'cta'   => ['label' => 'CTA Banner',     'fields' => ['title', 'description']],
            ],
        ],
        'about' => [
            'label'    => 'About Us',
            'sections' => [
                'intro'   => ['label' => 'Introduction',   'fields' => ['heading', 'paragraph1', 'paragraph2', 'paragraph3']],
                'story'   => ['label' => 'Our Story',      'fields' => ['title', 'paragraph1', 'paragraph2', 'paragraph3', 'ambition', 'tagline']],
                'mission' => ['label' => 'Vision & Mission','fields' => ['vision_title', 'vision', 'mission_title', 'mission']],
                'values'  => ['label' => 'Core Values',    'fields' => ['title']],
                'cta'     => ['label' => 'CTA Banner',     'fields' => ['title', 'description']],
            ],
        ],
        'products' => [
            'label'    => 'Products Page',
            'sections' => [
                'header' => ['label' => 'Page Header', 'fields' => ['title', 'subtitle']],
            ],
        ],
        'articles' => [
            'label'    => 'Articles Page',
            'sections' => [
                'header' => ['label' => 'Page Header', 'fields' => ['title', 'subtitle']],
            ],
        ],
        'contact' => [
            'label'    => 'Contact Page',
            'sections' => [
                'header'  => ['label' => 'Page Header',  'fields' => ['title', 'subtitle']],
                'info'    => ['label' => 'Contact Info',  'fields' => ['address', 'email', 'website']],
            ],
        ],
    ];

    public function index()
    {
        $pages = self::PAGES;
        return view('admin.pages.index', compact('pages'));
    }

    public function edit(string $page)
    {
        abort_unless(array_key_exists($page, self::PAGES), 404);
        $config = self::PAGES[$page];

        // Load all existing values for this page
        $rows = PageSection::where('page', $page)->with('media')->get();
        $values = [];
        $mediaMap = [];
        foreach ($rows as $row) {
            $values[$row->section][$row->field] = $row->value;
            if ($row->media_id) {
                $mediaMap[$row->section][$row->field] = $row->media;
            }
        }

        $allMedia = Media::latest()->get();

        return view('admin.pages.edit', compact('page', 'config', 'values', 'mediaMap', 'allMedia'));
    }

    public function update(Request $request, string $page)
    {
        abort_unless(array_key_exists($page, self::PAGES), 404);
        $config = self::PAGES[$page];

        foreach ($config['sections'] as $sectionKey => $sectionConfig) {
            foreach ($sectionConfig['fields'] as $field) {
                $inputKey = $sectionKey . '__' . $field;
                $mediaKey = $sectionKey . '__' . $field . '__media_id';

                $value   = $request->input($inputKey);
                $mediaId = $request->integer($mediaKey) ?: null;

                PageSection::set($page, $sectionKey, $field, $value, $mediaId);
            }

            // Handle image fields for this section
            $imageMediaKey = $sectionKey . '__image__media_id';
            if ($request->filled($imageMediaKey)) {
                $mediaId = $request->integer($imageMediaKey);
                PageSection::set($page, $sectionKey, 'image', null, $mediaId);
            }
        }

        return redirect()->route('admin.pages.edit', $page)->with('success', 'Page content updated successfully.');
    }

    public static function pages(): array
    {
        return self::PAGES;
    }
}
