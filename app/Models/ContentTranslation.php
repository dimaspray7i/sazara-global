<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentTranslation extends Model
{
    protected $fillable = [
        'content_type', 'content_id', 'field', 'language', 'value',
    ];

    /**
     * Helper to set or update a translation.
     */
    public static function set(string $contentType, string|int $contentId, string $field, string $language, ?string $value): self
    {
        return static::updateOrCreate(
            [
                'content_type' => $contentType,
                'content_id'   => (string) $contentId,
                'field'        => $field,
                'language'     => strtolower($language),
            ],
            [
                'value'        => $value,
            ]
        );
    }
}
