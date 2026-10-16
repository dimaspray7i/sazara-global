<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ContentTranslation extends Model
{
    protected $fillable = [
        'content_type', 'content_id', 'field', 'language', 'value',
    ];

    protected static function booted(): void
    {
        static::saved(function ($model) {
            $lang = strtolower($model->language);
            $cacheKey = "tr_{$model->content_type}_{$model->content_id}_{$model->field}_{$lang}";
            if (filled($model->value)) {
                Cache::put($cacheKey, $model->value, 86400);
            } else {
                Cache::forget($cacheKey);
            }
        });

        static::deleted(function ($model) {
            $lang = strtolower($model->language);
            $cacheKey = "tr_{$model->content_type}_{$model->content_id}_{$model->field}_{$lang}";
            Cache::forget($cacheKey);
        });
    }

    /**
     * Helper to set or update a translation.
     */
    public static function set(string $contentType, string|int $contentId, string $field, string $language, ?string $value): self
    {
        $lang = strtolower($language);
        $contentIdStr = (string) $contentId;
        $cacheKey = "tr_{$contentType}_{$contentIdStr}_{$field}_{$lang}";

        if (filled($value)) {
            Cache::put($cacheKey, $value, 86400);
        } else {
            Cache::forget($cacheKey);
        }

        return static::updateOrCreate(
            [
                'content_type' => $contentType,
                'content_id'   => $contentIdStr,
                'field'        => $field,
                'language'     => $lang,
            ],
            [
                'value'        => $value,
            ]
        );
    }
}
