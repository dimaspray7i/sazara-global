<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Gallery extends Model
{
    protected $fillable = [
        'title', 'title_id',
        'description', 'description_id',
        'category_slug',
        'type',      // single | group
        'status',    // draft | published
        'cover_media_id',
        'sort_order',
        'media_count',
    ];

    protected $casts = [
        'sort_order'  => 'integer',
        'media_count' => 'integer',
    ];

    // ── Scopes ──────────────────────────────────────────────────────

    public function scopePublished($q)
    {
        return $q->where('status', 'published');
    }

    public function scopeDraft($q)
    {
        return $q->where('status', 'draft');
    }

    public function scopeCategory($q, ?string $slug)
    {
        return filled($slug) ? $q->where('category_slug', $slug) : $q;
    }

    // ── Relationships ────────────────────────────────────────────────

    /** Cover image */
    public function coverMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_media_id');
    }

    /** All media in this gallery, ordered */
    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'gallery_media')
            ->withPivot('sort_order')
            ->orderBy('gallery_media.sort_order');
    }

    /** Category model */
    public function category(): BelongsTo
    {
        return $this->belongsTo(GalleryCategory::class, 'category_slug', 'slug');
    }

    // ── Helpers ──────────────────────────────────────────────────────

    /** Multilingual title */
    public function tr(string $field): string
    {
        $locale = app()->getLocale();
        if ($locale === 'id') {
            $col = $field . '_id';
            if (filled($this->$col)) return $this->$col;
        }
        return (string) ($this->$field ?? '');
    }

    /** Cover URL: use cover_media, or first media, or placeholder */
    public function coverUrl(): string
    {
        if ($this->cover_media_id && $this->coverMedia) {
            return $this->coverMedia->url();
        }
        // Lazy-load first media as fallback
        $first = $this->media()->first();
        if ($first) return $first->url();
        return asset('images/placeholder-gallery.svg');
    }

    /** Recalculate and persist media_count */
    public function syncMediaCount(): void
    {
        $this->update(['media_count' => $this->media()->count()]);
    }

    /** Is this gallery a group (multiple media)? */
    public function isGroup(): bool
    {
        return $this->type === 'group';
    }

    /** Is published? */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
}
