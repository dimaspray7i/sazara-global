<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ContentTranslation;
use App\Models\PageSection;
use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * ContentTranslationSeeder
 *
 * Populates content_translations with Indonesian ('id') translations
 * sourced from existing bilingual columns (*_id) in Products, Articles,
 * and PageSections.
 *
 * Rules:
 * - ONLY inserts rows that do NOT yet exist (preserves admin edits).
 * - NEVER calls an external translation API.
 * - NEVER inserts placeholder/English text as a "translation".
 * - Safe to run multiple times (idempotent).
 * - Only seeds 'id' locale — other locales must be filled via admin
 *   UI auto-translate or `php artisan translations:seed`.
 */
class ContentTranslationSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Indonesian (id) translations from bilingual columns...');

        $inserted = 0;

        // ------------------------------------------------------------------
        // 1. Products: name_id, description_id
        // ------------------------------------------------------------------
        foreach (Product::all(['id', 'name', 'description', 'name_id', 'description_id']) as $product) {
            $pairs = [
                'name'        => [(string) $product->name,        (string) ($product->name_id ?? '')],
                'description' => [(string) $product->description, (string) ($product->description_id ?? '')],
            ];

            foreach ($pairs as $field => [$enValue, $idValue]) {
                // Skip: no Indonesian value, or it is identical to English (not a real translation)
                if ($idValue === '' || $idValue === $enValue) {
                    continue;
                }

                $exists = ContentTranslation::where('content_type', 'product')
                    ->where('content_id', (string) $product->id)
                    ->where('field', $field)
                    ->where('language', 'id')
                    ->exists();

                if (! $exists) {
                    ContentTranslation::set('product', $product->id, $field, 'id', $idValue);
                    $inserted++;
                }
            }
        }

        // ------------------------------------------------------------------
        // 2. Articles: title_id, excerpt_id, body_id
        // ------------------------------------------------------------------
        foreach (Article::all(['id', 'title', 'excerpt', 'body', 'title_id', 'excerpt_id', 'body_id']) as $article) {
            $pairs = [
                'title'   => [(string) $article->title,   (string) ($article->title_id ?? '')],
                'excerpt' => [(string) $article->excerpt, (string) ($article->excerpt_id ?? '')],
                'body'    => [(string) $article->body,    (string) ($article->body_id ?? '')],
            ];

            foreach ($pairs as $field => [$enValue, $idValue]) {
                if ($idValue === '' || $idValue === $enValue) {
                    continue;
                }

                $exists = ContentTranslation::where('content_type', 'article')
                    ->where('content_id', (string) $article->id)
                    ->where('field', $field)
                    ->where('language', 'id')
                    ->exists();

                if (! $exists) {
                    ContentTranslation::set('article', $article->id, $field, 'id', $idValue);
                    $inserted++;
                }
            }
        }

        // ------------------------------------------------------------------
        // 3. PageSections: value_id column (if it exists)
        //    PageSection table may or may not have value_id — check first.
        // ------------------------------------------------------------------
        if (\Illuminate\Support\Facades\Schema::hasColumn('page_sections', 'value_id')) {
            foreach (PageSection::whereNotIn('page', ['settings'])->get() as $ps) {
                $enValue = (string) ($ps->value ?? '');
                $idValue = (string) ($ps->value_id ?? '');

                if ($idValue === '' || $idValue === $enValue) {
                    continue;
                }

                $contentId = "{$ps->page}.{$ps->section}.{$ps->field}";

                $exists = ContentTranslation::where('content_type', 'page_section')
                    ->where('content_id', $contentId)
                    ->where('field', $ps->field)
                    ->where('language', 'id')
                    ->exists();

                if (! $exists) {
                    ContentTranslation::set('page_section', $contentId, $ps->field, 'id', $idValue);
                    $inserted++;
                }
            }
        }

        $this->command->info("ContentTranslationSeeder: {$inserted} Indonesian rows inserted (existing rows preserved).");
        $this->command->line('  Note: For other languages (zh, ja, ko, ar, etc.) run:');
        $this->command->line('        php artisan translations:seed');
        $this->command->line('  Or use Admin Panel → Languages & Translations → Generate.');
    }
}
