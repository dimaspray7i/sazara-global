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

    /**
     * Get a global site setting.
     */
    public static function getSetting(string $field, mixed $default = null): string
    {
        $val = static::where('page', 'settings')
            ->where('section', 'general')
            ->where('field', $field)
            ->value('value');

        return ($val !== null && $val !== '') ? $val : ($default ?? config("sazara.{$field}", ''));
    }

    /**
     * Get the sanitized WhatsApp number for wa.me URL (e.g. 6281260407208).
     */
    public static function getCleanWhatsApp(): string
    {
        $raw = static::getSetting('whatsapp_number', config('sazara.whatsapp_number', '6281260407208'));
        $clean = preg_replace('/[^0-9]/', '', $raw);
        if (str_starts_with($clean, '0')) {
            $clean = '62' . substr($clean, 1);
        }
        return $clean ?: '6281260407208';
    }

    /**
     * Build standard WhatsApp URL with message.
     */
    public static function getWhatsAppUrl(?string $message = null): string
    {
        $clean = static::getCleanWhatsApp();
        $msg = $message ?: static::getSetting('whatsapp_message', config('sazara.whatsapp_message', 'Hello Sazara Global, I would like to make an inquiry...'));
        return "https://wa.me/{$clean}?text=" . rawurlencode($msg);
    }

    /** Relationship to media */
    public function media()
    {
        return $this->belongsTo(Media::class);
    }
}
