<?php

namespace App\Console\Commands;

use App\Services\TranslationService;
use Illuminate\Console\Command;

class SeedTranslations extends Command
{
    protected $signature = 'translations:seed';
    protected $description = 'Pre-seed core UI translations for all active languages';

    public function handle(): int
    {
        // Get all active languages excluding static file languages (en, id)
        $langs = \App\Models\Language::where('is_active', true)
            ->whereNotIn('code', ['en', 'id'])
            ->orderBy('sort_order', 'asc')
            ->pluck('code')
            ->toArray();

        if (empty($langs)) {
            $langs = ['zh', 'ja', 'ko', 'ar', 'es', 'fr', 'de', 'pt', 'it', 'ru', 'hi', 'th', 'vi'];
        }

        // Dynamically load all string keys from lang/en/ui.php
        $enUi = config('ui', []);
        if (empty($enUi) && file_exists(lang_path('en/ui.php'))) {
            $enUi = require lang_path('en/ui.php');
        }

        $flatten = function ($array, $prefix = 'ui.') use (&$flatten) {
            $result = [];
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    // Check if numeric indexed list of tuples (e.g. flow, why)
                    if (array_is_list($value)) {
                        foreach ($value as $idx => $item) {
                            if (is_array($item)) {
                                foreach ($item as $subIdx => $subVal) {
                                    if (is_string($subVal)) {
                                        $result["{$prefix}{$key}.{$idx}.{$subIdx}"] = $subVal;
                                    }
                                }
                            } elseif (is_string($item)) {
                                $result["{$prefix}{$key}.{$idx}"] = $item;
                            }
                        }
                    } else {
                        $result = array_merge($result, $flatten($value, "{$prefix}{$key}."));
                    }
                } elseif (is_string($value)) {
                    $result["{$prefix}{$key}"] = $value;
                }
            }
            return $result;
        };

        $keys = $flatten($enUi);

        $total = count($langs) * count($keys);
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $seededCount = 0;
        $skippedCount = 0;

        foreach ($langs as $lang) {
            $this->line('');
            $this->info("Checking translations for: [{$lang}]");
            foreach ($keys as $key => $en) {
                // Check if already exists in DB (DO NOT overwrite admin edits)
                $existing = \App\Models\ContentTranslation::where('content_type', 'ui')
                    ->where('content_id', $key)
                    ->where('field', 'text')
                    ->where('language', $lang)
                    ->exists();

                if ($existing) {
                    $skippedCount++;
                    $bar->advance();
                    continue;
                }

                try {
                    $translated = TranslationService::autoTranslate($en, 'en', $lang);
                    if (filled($translated) && $translated !== $en) {
                        \App\Models\ContentTranslation::set('ui', $key, 'text', $lang, $translated);
                        $seededCount++;
                    }
                    usleep(150000); // 150ms throttle
                } catch (\Throwable $e) {
                    $this->warn("  Failed [{$lang}] {$key}: " . $e->getMessage());
                }
                $bar->advance();
            }
        }

        $bar->finish();
        $this->line('');
        $this->info("Translation seeding complete! Newly added: {$seededCount}, Preserved existing: {$skippedCount}");

        return self::SUCCESS;
    }
}