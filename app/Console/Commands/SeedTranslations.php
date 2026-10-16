<?php

namespace App\Console\Commands;

use App\Services\TranslationService;
use Illuminate\Console\Command;

/**
 * translations:seed
 *
 * Pre-seeds UI translations via external API for all active non-static languages.
 * - Skips keys that already have a DB translation (preserves admin edits).
 * - Throttled: 150ms between API calls.
 * - Per-language time budget: stops a language if it exceeds --lang-timeout seconds
 *   (default 300s / 5 min) so the CLI doesn't hang indefinitely.
 * - Can be resumed: already-seeded rows are skipped on re-run.
 * - Can target a single language: --lang=zh
 *
 * Usage:
 *   php artisan translations:seed                  # all active non-static languages
 *   php artisan translations:seed --lang=zh        # only Chinese
 *   php artisan translations:seed --lang-timeout=120 --lang=ar
 */
class SeedTranslations extends Command
{
    protected $signature = 'translations:seed
                            {--lang= : Only seed a specific language code (e.g. zh)}
                            {--lang-timeout=300 : Max seconds per language before moving to next}';

    protected $description = 'Pre-seed UI translations via API for all active languages (skips existing rows)';

    public function handle(): int
    {
        $langTimeout = max(30, (int) $this->option('lang-timeout'));
        $onlyLang    = $this->option('lang') ? strtolower(trim($this->option('lang'))) : null;

        // Get target languages
        $query = \App\Models\Language::where('is_active', true)
            ->whereNotIn('code', ['en', 'id'])
            ->orderBy('sort_order', 'asc');

        if ($onlyLang) {
            $query->where('code', $onlyLang);
        }

        $langs = $query->pluck('code')->toArray();

        if (empty($langs)) {
            $this->warn('No active non-static languages found. Nothing to seed.');
            return self::SUCCESS;
        }

        // Load full queue of translatable items (UI keys, products, articles, page_sections, gallery)
        $queueController = new \App\Http\Controllers\Admin\TranslationController();
        $queue = $queueController->buildQueue();

        if (empty($queue)) {
            $this->error('Translation queue is empty.');
            return self::FAILURE;
        }

        $this->info(sprintf(
            'Translating %d items across entire website for %d language(s): %s',
            count($queue),
            count($langs),
            implode(', ', $langs)
        ));
        $this->line(sprintf('  Per-language timeout: %ds. Re-run to continue any interrupted language.', $langTimeout));
        $this->line('');

        $totalSeeded  = 0;
        $totalSkipped = 0;
        $totalFailed  = 0;

        foreach ($langs as $lang) {
            $this->info("── Language: [{$lang}]");

            // Load all existing keys for this language in one query
            $existingKeys = \App\Models\ContentTranslation::where('language', $lang)
                ->get(['content_type', 'content_id', 'field'])
                ->map(fn($r) => "{$r->content_type}|{$r->content_id}|{$r->field}")
                ->flip()
                ->all();

            $langSeeded  = 0;
            $langSkipped = 0;
            $langFailed  = 0;
            $langStart   = time();

            $bar = $this->output->createProgressBar(count($queue));
            $bar->start();

            foreach ($queue as $item) {
                // Check per-language time budget
                if ((time() - $langStart) >= $langTimeout) {
                    $this->line('');
                    $this->warn("  [{$lang}] Time budget ({$langTimeout}s) reached. Re-run to continue.");
                    break;
                }

                $lookupKey = "{$item['type']}|{$item['id']}|{$item['field']}";

                // Already in DB — skip (preserve admin edits)
                if (isset($existingKeys[$lookupKey])) {
                    $langSkipped++;
                    $bar->advance();
                    continue;
                }

                $en = (string) ($item['text'] ?? '');

                // Skip values that should not be translated
                if ($en === '' || $this->shouldSkip($en)) {
                    $langSkipped++;
                    $bar->advance();
                    continue;
                }

                try {
                    $translated = TranslationService::autoTranslate(mb_substr($en, 0, 500), 'en', $lang);
                    if (filled($translated) && $translated !== $en) {
                        \App\Models\ContentTranslation::set($item['type'], $item['id'], $item['field'], $lang, $translated);
                        $existingKeys[$lookupKey] = true;
                        $langSeeded++;
                    } else {
                        $langSkipped++;
                    }
                    usleep(150_000); // 150ms — MyMemory free tier throttle
                } catch (\Throwable $e) {
                    $langFailed++;
                    $this->line('');
                    $this->warn("  Failed [{$lang}] {$lookupKey}: " . $e->getMessage());
                }

                $bar->advance();
            }

            $bar->finish();
            $this->line('');
            $this->line(sprintf(
                "  Done [%s]: +%d inserted, %d skipped, %d failed.",
                $lang, $langSeeded, $langSkipped, $langFailed
            ));
            $this->line('');

            $totalSeeded  += $langSeeded;
            $totalSkipped += $langSkipped;
            $totalFailed  += $langFailed;
        }

        $this->info("Translation seeding complete!");
        $this->line("  Total inserted : {$totalSeeded}");
        $this->line("  Total skipped  : {$totalSkipped} (already existed or not translatable)");
        $this->line("  Total failed   : {$totalFailed}");

        return self::SUCCESS;
    }

    /**
     * Flatten nested lang array into dot-notation keys.
     */
    protected function flattenKeys(array $array, string $prefix = 'ui.'): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (array_is_list($value)) {
                    foreach ($value as $idx => $item) {
                        if (is_array($item)) {
                            foreach ($item as $subKey => $subVal) {
                                if (is_string($subVal) && $subVal !== '') {
                                    $result["{$prefix}{$key}.{$idx}.{$subKey}"] = $subVal;
                                }
                            }
                        } elseif (is_string($item) && $item !== '') {
                            $result["{$prefix}{$key}.{$idx}"] = $item;
                        }
                    }
                } else {
                    $result = array_merge($result, $this->flattenKeys($value, "{$prefix}{$key}."));
                }
            } elseif (is_string($value) && $value !== '') {
                $result["{$prefix}{$key}"] = $value;
            }
        }
        return $result;
    }

    /**
     * Values that must NOT be machine-translated.
     */
    protected function shouldSkip(string $text): bool
    {
        $text = trim($text);
        if (mb_strlen($text) <= 3) return true;
        if (filter_var($text, FILTER_VALIDATE_EMAIL)) return true;
        if (filter_var($text, FILTER_VALIDATE_URL)) return true;
        if (preg_match('/^\+?[\d\s\-\(\)]{4,}$/', $text)) return true;
        if (preg_match('/^(PT\s+)?Sazara(\s+Global(\s+Trade)?)?$/i', $text)) return true;
        if (preg_match('/\.(jpe?g|png|webp|gif|svg|ico)$/i', $text)) return true;
        if (str_starts_with($text, '/') && !str_contains($text, ' ')) return true;
        return false;
    }
}