<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'filename', 'original_name', 'mime_type', 'size', 'path', 'file_hash', 'alt',
        'title', 'title_id', 'caption', 'caption_id', 'category',
        'is_public', 'is_gallery', 'sort_order',
    ];

    protected $casts = [
        'is_public'   => 'boolean',
        'is_gallery'  => 'boolean',
        'sort_order'  => 'integer',
    ];

    /**
     * Scope: media available for general use (usable in products, articles, pages).
     */
    public function scopePublic($q)
    {
        return $q->where('is_public', true);
    }

    /**
     * Scope: media explicitly selected for the public Gallery.
     * Must be both published (is_public) AND marked for gallery (is_gallery).
     */
    public function scopeGallery($q)
    {
        return $q->where('is_public', true)->where('is_gallery', true);
    }

    public function scopeCategory($q, ?string $cat)
    {
        return filled($cat) ? $q->where('category', $cat) : $q;
    }

    public static function isFilenameLike(?string $str): bool
    {
        if (blank($str)) {
            return false;
        }
        if (preg_match('/^(WhatsApp|IMG|VID|DSC|PXL|DJI|screenshot|screen_shot|photo)[_\s-]/i', $str)) {
            return true;
        }
        if (preg_match('/\.(jpe?g|png|webp|gif|mp4|mov|avi|wmv)$/i', $str)) {
            return true;
        }
        return false;
    }

    /** Multilingual field accessor with English fallback */
    public function tr(string $field): string
    {
        $locale = app()->getLocale();
        $val = '';
        if ($locale === 'id') {
            $column = $field . '_id';
            if (filled($this->$column)) {
                $val = $this->$column;
            }
        }
        if (blank($val) && filled($this->$field)) {
            $val = $this->$field;
        }

        // Never leak filename-like titles to public
        if ($field === 'title' && (self::isFilenameLike($val) || $val === pathinfo($this->original_name ?? '', PATHINFO_FILENAME))) {
            return '';
        }

        return \App\Services\TranslationService::get('media', $this->id, $field, $val, $locale);
    }

    /** Full public URL for the image */
    public function url(): string
    {
        return asset('storage/' . $this->path);
    }

    /** Human-readable file size */
    public function humanSize(): string
    {
        $bytes = $this->size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }

    /** Check if this media is referenced by any page_sections, products, or articles */
    public function isInUse(): bool
    {
        if (PageSection::where('media_id', $this->id)->exists()) return true;
        if (Product::where('image', $this->path)->exists()) return true;
        if (Article::where('thumbnail', $this->path)->exists()) return true;
        return false;
    }

    /** Relationships */
    public function pageSections()
    {
        return $this->hasMany(PageSection::class);
    }
}
