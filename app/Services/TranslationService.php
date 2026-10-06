<?php

namespace App\Services;

use App\Models\ContentTranslation;
use App\Models\Language;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TranslationService
{
    /**
     * In-memory cache for the current request lifecycle.
     */
    protected static array $runtimeCache = [];

    /**
     * Map internal 2-letter codes to standard target language codes for translation APIs.
     */
    protected static array $langCodeMap = [
        'zh' => 'zh-CN',
        'he' => 'he',
        'ar' => 'ar',
        'ja' => 'ja',
        'ko' => 'ko',
        'es' => 'es',
        'fr' => 'fr',
        'de' => 'de',
        'pt' => 'pt',
        'it' => 'it',
        'ru' => 'ru',
        'hi' => 'hi',
        'th' => 'th',
        'vi' => 'vi',
        'id' => 'id',
        'en' => 'en',
    ];

    /**
     * Get translation for a given content type, id, field, and locale.
     * Fallback order:
     * 1. If locale is default (en) and original value provided, return original value.
     * 2. Database/Cache translation for requested locale.
     * 3. Translation Provider / API (if configured or free translator).
     * 4. Fallback to original value (or English default).
     */
    public static function get(
        string $contentType,
        string|int $contentId,
        string $field,
        ?string $originalFallback = '',
        ?string $locale = null
    ): string {
        $locale = strtolower($locale ?: app()->getLocale());
        $contentId = (string) $contentId;

        // If English and an English original value is supplied, use it directly
        if ($locale === 'en' && filled($originalFallback)) {
            return (string) $originalFallback;
        }

        $cacheKey = "tr_{$contentType}_{$contentId}_{$field}_{$locale}";

        if (array_key_exists($cacheKey, self::$runtimeCache)) {
            return self::$runtimeCache[$cacheKey];
        }

        // 1. Check persistent Cache / Database
        $translated = Cache::remember($cacheKey, 86400, function () use ($contentType, $contentId, $field, $locale) {
            return ContentTranslation::where('content_type', $contentType)
                ->where('content_id', $contentId)
                ->where('field', $field)
                ->where('language', $locale)
                ->value('value');
        });

        if (filled($translated)) {
            self::$runtimeCache[$cacheKey] = $translated;
            return $translated;
        }

        // 2. Auto-translate on the fly — only in console (artisan) context to avoid HTTP timeout.
        // During web requests, if no DB translation found, fall back to original silently.
        $isConsole = app()->runningInConsole();
        if ($isConsole && filled($originalFallback) && config('sazara.auto_translate', true)) {
            $auto = self::autoTranslate((string) $originalFallback, 'en', $locale);
            if (filled($auto) && $auto !== $originalFallback) {
                ContentTranslation::set($contentType, $contentId, $field, $locale, $auto);
                Cache::put($cacheKey, $auto, 86400);
                self::$runtimeCache[$cacheKey] = $auto;
                return $auto;
            }
        }

        // 3. Fallback to original
        self::$runtimeCache[$cacheKey] = (string) $originalFallback;
        return (string) $originalFallback;
    }

    /**
     * Translate text via configured provider or lightweight fallback.
     */
    public static function autoTranslate(string $text, string $source = 'en', string $target = 'id'): ?string
    {
        $text = trim($text);
        if ($text === '' || $source === $target) {
            return $text;
        }

        $sourceCode = self::$langCodeMap[$source] ?? $source;
        $targetCode = self::$langCodeMap[$target] ?? $target;

        $provider = config('sazara.translation_provider', env('TRANSLATION_PROVIDER', 'mymemory'));
        $apiKey   = env('TRANSLATION_API_KEY');

        try {
            // Google Translate API v2 (if key provided)
            if ($provider === 'google' && filled($apiKey)) {
                $response = Http::withoutVerifying()
                    ->timeout(4)
                    ->post('https://translation.googleapis.com/language/translate/v2', [
                        'q'      => $text,
                        'source' => $sourceCode,
                        'target' => $targetCode,
                        'format' => 'text',
                        'key'    => $apiKey,
                    ]);

                if ($response->successful()) {
                    return $response->json('data.translations.0.translatedText') ?? $text;
                }
            }

            // MyMemory Translation API
            $email = env('SITE_EMAIL', 'contact@sazaraglobal.com');
            $langpair = "{$sourceCode}|{$targetCode}";

            $response = Http::withoutVerifying()
                ->timeout(4)
                ->get('https://api.mymemory.translated.net/get', [
                    'q'        => mb_substr($text, 0, 500),
                    'langpair' => $langpair,
                    'de'       => $email,
                ]);

            if ($response->successful() && $response->json('responseStatus') == 200) {
                $translated = $response->json('responseData.translatedText');
                if (filled($translated) && ! str_contains(strtoupper($translated), 'QUERY LENGTH LIMIT EXCEEDED')) {
                    return html_entity_decode($translated, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Auto-translation failed for target [{$target}]: " . $e->getMessage());
        }

        return $text;
    }

    /**
     * Clear all cached translations for a specific model or key.
     */
    public static function clearCache(string $contentType, string|int $contentId, string $field): void
    {
        $languages = Language::getSupportedCodes();
        foreach ($languages as $code) {
            Cache::forget("tr_{$contentType}_{$contentId}_{$field}_{$code}");
            unset(self::$runtimeCache["tr_{$contentType}_{$contentId}_{$field}_{$code}"]);
        }
    }
}
