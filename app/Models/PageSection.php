<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageSection extends Model
{
    protected $fillable = ['page', 'section', 'field', 'value', 'media_id'];

    /**
     * Get a single field value for a given page/section/field.
     * Returns $default if not found.
     */
    public static function get(string $page, string $section, string $field, mixed $default = ''): string
    {
        return static::where('page', $page)
            ->where('section', $section)
            ->where('field', $field)
            ->value('value') ?? $default;
    }

    /**
     * Get the media model attached to a given page/section/field.
     */
    public static function getMedia(string $page, string $section, string $field): ?Media
    {
        $row = static::where('page', $page)
            ->where('section', $section)
            ->where('field', $field)
            ->with('media')
            ->first();

        return $row?->media;
    }

    /**
     * Get all fields of a section as key→value array.
     */
    public static function getSection(string $page, string $section): array
    {
        return static::where('page', $page)
            ->where('section', $section)
            ->get()
            ->pluck('value', 'field')
            ->toArray();
    }

    /**
     * Upsert a value.
     */
    public static function set(string $page, string $section, string $field, ?string $value, ?int $mediaId = null): void
    {
        static::updateOrCreate(
            ['page' => $page, 'section' => $section, 'field' => $field],
            ['value' => $value, 'media_id' => $mediaId]
        );
    }

    /** Relationship to media */
    public function media()
    {
        return $this->belongsTo(Media::class);
    }
}
