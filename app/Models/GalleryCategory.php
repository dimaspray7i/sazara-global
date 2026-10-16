<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryCategory extends Model
{
    protected $fillable = ['slug', 'name', 'name_id', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->orderBy('sort_order');
    }

    /** Multilingual label supporting all active locales (zh, ja, id, en, etc.) */
    public function label(?string $locale = null): string
    {
        $locale = strtolower($locale ?: app()->getLocale());

        if ($locale === 'en') {
            return $this->name;
        }

        if ($locale === 'id' && filled($this->name_id)) {
            return $this->name_id;
        }

        // 1. Check ContentTranslation DB for this category by ID
        $translated = \App\Services\TranslationService::get('gallery_category', $this->id, 'name', '', $locale);
        if (filled($translated) && $translated !== $this->name) {
            return $translated;
        }

        // 2. Check ContentTranslation DB by slug
        $translatedSlug = \App\Services\TranslationService::get('gallery_category', $this->slug, 'name', '', $locale);
        if (filled($translatedSlug) && $translatedSlug !== $this->name) {
            return $translatedSlug;
        }

        // 3. Check UI translation key ui.gallery.{slug}
        $uiKey = "ui.gallery.{$this->slug}";
        $uiTrans = __($uiKey, [], $locale);
        if ($uiTrans !== $uiKey && !str_ends_with($uiTrans, ".{$this->slug}") && filled($uiTrans) && $uiTrans !== $this->name) {
            return $uiTrans;
        }

        return ($locale === 'id' && filled($this->name_id)) ? $this->name_id : $this->name;
    }

    /**
     * Synchronize and cache translations for this category across active languages
     */
    public function syncTranslations(?array $locales = null): void
    {
        $targetLangs = $locales ?: Language::where('is_active', true)->whereNotIn('code', ['en', 'id'])->pluck('code')->toArray();

        foreach ($targetLangs as $lang) {
            $existing = ContentTranslation::where('content_type', 'gallery_category')
                ->where('content_id', (string) $this->id)
                ->where('field', 'name')
                ->where('language', $lang)
                ->value('value');

            if (blank($existing)) {
                $translated = \App\Services\TranslationService::autoTranslate($this->name, 'en', $lang);
                if (filled($translated) && $translated !== $this->name) {
                    ContentTranslation::set('gallery_category', $this->id, 'name', $lang, $translated);
                    ContentTranslation::set('gallery_category', $this->slug, 'name', $lang, $translated);
                    ContentTranslation::set('ui', "ui.gallery.{$this->slug}", 'text', $lang, $translated);
                }
            } else {
                ContentTranslation::set('gallery_category', $this->slug, 'name', $lang, $existing);
                ContentTranslation::set('ui', "ui.gallery.{$this->slug}", 'text', $lang, $existing);
            }
        }
    }

    protected static function booted()
    {
        static::saved(function ($cat) {
            \App\Services\TranslationService::clearCache('gallery_category', $cat->id, 'name');
            \App\Services\TranslationService::clearCache('gallery_category', $cat->slug, 'name');
        });

        static::deleted(function ($cat) {
            \App\Services\TranslationService::clearCache('gallery_category', $cat->id, 'name');
            \App\Services\TranslationService::clearCache('gallery_category', $cat->slug, 'name');
            ContentTranslation::where('content_type', 'gallery_category')
                ->whereIn('content_id', [(string) $cat->id, $cat->slug])
                ->delete();
            ContentTranslation::where('content_type', 'ui')
                ->where('content_id', "ui.gallery.{$cat->slug}")
                ->delete();
        });
    }

    /** Get all active categories as slug => label array (for views) */
    public static function activeMap(): array
    {
        return static::active()->get()->mapWithKeys(fn ($c) => [$c->slug => $c->label()])->toArray();
    }

    /** Get all active categories as slug => name_en array (for seeding, etc.) */
    public static function allActive(): \Illuminate\Database\Eloquent\Collection
    {
        return static::active()->get();
    }
}
