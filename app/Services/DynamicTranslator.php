<?php

namespace App\Services;

use App\Models\ContentTranslation;
use Illuminate\Translation\Translator;

/**
 * DynamicTranslator extends Laravel Translator so locales without static .php
 * lang files (zh, ja, ko, ar, es, fr, de, ...) look up DB-cached translations.
 *
 * During web requests, if no DB translation is found, falls back to English
 * WITHOUT calling the external API (to avoid 30s HTTP timeout).
 * Use: php artisan translations:seed  to pre-populate the DB.
 */
class DynamicTranslator extends Translator
{
    /** Locales that already have static .php files - no interception needed */
    protected array $staticLocales = ['en', 'id'];

    /** In-memory cache for current request */
    protected static array $hitCache = [];
    protected static array $missCache = [];

    public function get($key, array $replace = [], $locale = null, $fallback = true): mixed
    {
        $result = parent::get($key, $replace, $locale, $fallback);
        $resolvedLocale = $locale ?? $this->locale;

        if (in_array($resolvedLocale, $this->staticLocales, true)) {
            return $result;
        }

        // Get English fallback from static file
        $enResult = parent::get($key, $replace, 'en', false);

        if ($result === $key || $result === $enResult) {
            return $this->lookupFromDb($key, $enResult, $resolvedLocale, $replace);
        }

        return $result;
    }

    protected function lookupFromDb(string $key, mixed $enValue, string $locale, array $replace = []): mixed
    {
        if (is_array($enValue)) {
            return $this->lookupArrayFromDb($key, $enValue, $locale, $replace);
        }

        // Always return a string — never null
        if (! is_string($enValue)) {
            return $key;
        }

        $cacheKey = "tr_ui_{$key}_{$locale}";

        // Hit cache: already found a DB translation this request
        if (isset(static::$hitCache[$cacheKey])) {
            return $this->makeReplacements(static::$hitCache[$cacheKey], $replace);
        }

        // Miss cache: already confirmed not in DB this request, use English
        if (isset(static::$missCache[$cacheKey])) {
            return $this->makeReplacements($enValue, $replace);
        }

        // Query DB directly (no Cache::remember to avoid caching null)
        $translated = ContentTranslation::where('content_type', 'ui')
            ->where('content_id', $key)
            ->where('field', 'text')
            ->where('language', $locale)
            ->value('value');

        if (filled($translated)) {
            static::$hitCache[$cacheKey] = $translated;
            return $this->makeReplacements($translated, $replace);
        }

        // Not in DB — fall back to English, record miss
        static::$missCache[$cacheKey] = true;
        return $this->makeReplacements($enValue, $replace);
    }

    protected function lookupArrayFromDb(string $baseKey, array $items, string $locale, array $replace): array
    {
        $result = [];
        foreach ($items as $k => $v) {
            $childKey = "{$baseKey}.{$k}";
            if (is_array($v)) {
                $result[$k] = $this->lookupArrayFromDb($childKey, $v, $locale, $replace);
            } elseif (is_string($v) && $v !== '') {
                $result[$k] = $this->lookupFromDb($childKey, $v, $locale, $replace);
            } else {
                $result[$k] = $v;
            }
        }
        return $result;
    }
}
