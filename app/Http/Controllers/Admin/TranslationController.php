<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentTranslation;
use App\Models\Language;
use App\Services\TranslationService;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    /** Max translatable chars per item sent to API (MyMemory free limit: 500) */
    private const MAX_CHARS = 400;

    /** Items per HTTP request — keep low so request stays well under 30s.
     *  API avg ~1s/call => 5 items ~ 5-8s per request. */
    private const BATCH_SIZE = 5;

    public function index(Request $request)
    {
        $languages    = Language::orderBy("sort_order", "asc")->get();
        $selectedLang = $request->query("lang", "zh");
        $activeLang   = Language::where("code", $selectedLang)->first() ?? $languages->first();
        $translations = ContentTranslation::where("language", $activeLang?->code ?? "en")
            ->latest("updated_at")
            ->paginate(30)
            ->withQueryString();
        return view("admin.translations.index", compact("languages", "activeLang", "translations"));
    }

    public function storeLanguage(Request $request)
    {
        $validated = $request->validate([
            "code"        => ["required", "string", "max:10", "unique:languages,code"],
            "name"        => ["required", "string", "max:50"],
            "native_name" => ["required", "string", "max:50"],
            "flag"        => ["nullable", "string", "max:10"],
            "direction"   => ["required", "in:ltr,rtl"],
        ]);
        $validated["code"]       = strtolower($validated["code"]);
        $validated["is_active"]  = true;
        $validated["sort_order"] = Language::max("sort_order") + 1;
        Language::create($validated);
        return back()->with("success", "Language [{$validated["name"]}] added successfully.");
    }

    public function toggleLanguage(Language $language)
    {
        if ($language->code === "en") {
            return back()->with("error", "Default English language cannot be disabled.");
        }
        $language->update(["is_active" => ! $language->is_active]);
        return back()->with("success", "Language [{$language->name}] status updated.");
    }

    public function updateTranslation(Request $request, ContentTranslation $translation)
    {
        $validated = $request->validate(["value" => ["nullable", "string"]]);
        $translation->update($validated);
        TranslationService::clearCache($translation->content_type, $translation->content_id, $translation->field);
        return back()->with("success", "Translation updated successfully.");
    }

    /**
     * BATCH AUTO-TRANSLATE — max BATCH_SIZE items per HTTP request.
     *
     * POST params:
     *   target_lang  string  — language code
     *   offset       int     — queue offset (default 0)
     *   text         string  — (legacy) single inline translate, returns {translated}
     *
     * JSON response:
     *   done, translated, skipped, failed, offset, total, message
     */
    public function autoTranslateMissing(Request $request)
    {
        $targetLang = $request->input("target_lang");
        $text       = $request->input("text");
        $source     = $request->input("source", "en");

        // Legacy: single-text AJAX translate
        if (filled($text)) {
            return response()->json([
                "translated" => TranslationService::autoTranslate($text, $source, $targetLang),
            ]);
        }

        if (! filled($targetLang)) {
            return back()->with("error", "Target language not specified.");
        }

        $targetLang = strtolower(trim($targetLang));

        if (in_array($targetLang, ["en", "id"], true)) {
            return response()->json([
                "done" => true, "translated" => 0, "skipped" => 0, "failed" => 0,
                "offset" => 0, "total" => 0,
                "message" => "Language [{$targetLang}] uses static PHP translation files.",
            ]);
        }

        $queue  = $this->buildQueue();
        $offset = max(0, (int) $request->input("offset", 0));
        $total  = count($queue);
        $batch  = array_slice($queue, $offset, self::BATCH_SIZE);

        if (empty($batch)) {
            return response()->json([
                "done" => true, "translated" => 0, "skipped" => 0, "failed" => 0,
                "offset" => $offset, "total" => $total,
                "message" => "All items already processed for [{$targetLang}].",
            ]);
        }

        // One DB query to get all existing keys for this language
        $existingKeys = ContentTranslation::where("language", $targetLang)
            ->get(["content_type", "content_id", "field"])
            ->map(fn($r) => "{$r->content_type}|{$r->content_id}|{$r->field}")
            ->flip()
            ->all();

        $translated = 0;
        $skipped    = 0;
        $failed     = 0;

        foreach ($batch as $item) {
            $lookupKey = "{$item["type"]}|{$item["id"]}|{$item["field"]}";

            // Already exists in DB — skip (preserve manual edits)
            if (array_key_exists($lookupKey, $existingKeys)) {
                $skipped++;
                continue;
            }

            $enText = (string) ($item["text"] ?? "");

            // Dont translate: empty, too short, URL/email/number/brand
            if ($enText === "" || $this->shouldSkip($enText)) {
                $skipped++;
                continue;
            }

            // Truncate to API limit (no translation for content over limit; saves failed API call)
            $sendText = mb_substr($enText, 0, self::MAX_CHARS);

            // Attempt translation with 1 retry on failure
            $result = $this->translateWithRetry($sendText, "en", $targetLang);

            if ($result === null) {
                // API hard-failed both attempts
                $failed++;
                continue;
            }

            if ($result === $sendText) {
                // API returned source text unchanged = unsupported language pair or quota
                $skipped++;
                continue;
            }

            ContentTranslation::set($item["type"], $item["id"], $item["field"], $targetLang, $result);
            $existingKeys[$lookupKey] = true;
            $translated++;
            // No usleep — API latency itself is the natural throttle (~1s)
        }

        $nextOffset = $offset + self::BATCH_SIZE;
        $done       = $nextOffset >= $total;

        return response()->json([
            "done"       => $done,
            "translated" => $translated,
            "skipped"    => $skipped,
            "failed"     => $failed,
            "offset"     => $nextOffset,
            "total"      => $total,
            "message"    => $done
                ? "Done translating [{$targetLang}]."
                : "Batch {$nextOffset}/{$total}.",
        ]);
    }

    /**
     * Try translation once; on Throwable, wait 500ms and retry once.
     * Returns null only if both attempts throw an exception.
     * Returns $sendText (source) if API responds but returns unchanged text.
     */
    private function translateWithRetry(string $text, string $source, string $target): ?string
    {
        try {
            return TranslationService::autoTranslate($text, $source, $target);
        } catch (\Throwable) {
            usleep(500_000); // 500ms before retry
            try {
                return TranslationService::autoTranslate($text, $source, $target);
            } catch (\Throwable) {
                return null;
            }
        }
    }

    /**
     * Build flat list of all translatable items.
     * Caches result in memory for the duration of this request only.
     */
    public function buildQueue(): array
    {
        static $cache = null;
        if ($cache !== null) return $cache;

        $queue = [];

        // UI keys from lang/en/ui.php
        if (file_exists(lang_path("en/ui.php"))) {
            $enUi    = require lang_path("en/ui.php");
            $flatten = function (array $array, string $prefix = "ui.") use (&$flatten): array {
                $result = [];
                foreach ($array as $key => $value) {
                    if (is_array($value)) {
                        if (array_is_list($value)) {
                            foreach ($value as $idx => $item) {
                                if (is_array($item)) {
                                    foreach ($item as $subKey => $subVal) {
                                        if (is_string($subVal)) {
                                            $result["{$prefix}{$key}.{$idx}.{$subKey}"] = $subVal;
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
            foreach ($flatten($enUi) as $key => $enText) {
                $queue[] = ["type" => "ui", "id" => $key, "field" => "text", "text" => $enText];
            }
        }

        // Products
        foreach (\App\Models\Product::active()->get(["id", "name", "description", "specification"]) as $p) {
            foreach (["name", "description", "specification"] as $field) {
                $val = (string) ($p->$field ?? "");
                if ($val !== "") {
                    $queue[] = ["type" => "product", "id" => (string) $p->id, "field" => $field, "text" => $val];
                }
            }
        }

        // Articles — body truncated to MAX_CHARS for translation only
        foreach (\App\Models\Article::published()->get(["id", "title", "excerpt", "body"]) as $a) {
            foreach (["title", "excerpt", "body"] as $field) {
                $val = (string) ($a->$field ?? "");
                if ($val !== "") {
                    $queue[] = ["type" => "article", "id" => (string) $a->id, "field" => $field, "text" => $val];
                }
            }
        }

        // PageSections (skip settings page)
        foreach (\App\Models\PageSection::whereNotIn("page", ["settings"])->get() as $ps) {
            $val = (string) ($ps->value ?? "");
            if ($val !== "") {
                $contentId = "{$ps->page}.{$ps->section}.{$ps->field}";
                $queue[]   = ["type" => "page_section", "id" => $contentId, "field" => $ps->field, "text" => $val];
            }
        }

        $cache = $queue;
        return $cache;
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
        if (preg_match("/^\+?[\d\s\-\(\)]{4,}$/", $text)) return true;
        if (preg_match("/^(PT\s+)?Sazara(\s+Global(\s+Trade)?)?$/i", $text)) return true;
        return false;
    }
}