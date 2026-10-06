<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'filename', 'original_name', 'mime_type', 'size', 'path', 'alt',
        'title', 'title_id', 'caption', 'caption_id', 'category', 'is_public', 'sort_order',
    ];

    protected $casts = [
        'is_public'   => 'boolean',
        'sort_order'  => 'integer',
    ];

    public function scopePublic($q)
    {
        return $q->where('is_public', true);
    }

    public function scopeCategory($q, ?string $cat)
    {
        return filled($cat) ? $q->where('category', $cat) : $q;
    }

    /** Multilingual field accessor with English fallback */
    public function tr(string $field): string
    {
        $locale = app()->getLocale();
        if ($locale === 'id') {
            $column = $field . '_id';
            if (filled($this->$column)) {
                return $this->$column;
            }
        }
        $fallback = '';
        if (filled($this->$field)) {
            $fallback = $this->$field;
        } elseif ($field === 'title' || $field === 'alt') {
            $fallback = ucwords(str_replace(['-', '_', '.jpg', '.png', '.webp'], ' ', $this->original_name));
        }

        return \App\Services\TranslationService::get('media', $this->id, $field, $fallback, $locale);
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
