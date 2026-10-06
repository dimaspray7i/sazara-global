<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentTranslation;
use App\Models\Language;
use App\Services\TranslationService;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    public function index(Request $request)
    {
        $languages = Language::orderBy('sort_order', 'asc')->get();
        $selectedLang = $request->query('lang', 'zh');

        $activeLang = Language::where('code', $selectedLang)->first() ?? $languages->first();

        // Get recent translations for this language
        $translations = ContentTranslation::where('language', $activeLang?->code ?? 'en')
            ->latest('updated_at')
            ->paginate(30)
            ->withQueryString();

        return view('admin.translations.index', compact('languages', 'activeLang', 'translations'));
    }

    public function storeLanguage(Request $request)
    {
        $validated = $request->validate([
            'code'        => ['required', 'string', 'max:10', 'unique:languages,code'],
            'name'        => ['required', 'string', 'max:50'],
            'native_name' => ['required', 'string', 'max:50'],
            'flag'        => ['nullable', 'string', 'max:10'],
            'direction'   => ['required', 'in:ltr,rtl'],
        ]);

        $validated['code'] = strtolower($validated['code']);
        $validated['is_active'] = true;
        $validated['sort_order'] = Language::max('sort_order') + 1;

        Language::create($validated);

        return back()->with('success', "Language [{$validated['name']}] added successfully.");
    }

    public function toggleLanguage(Language $language)
    {
        if ($language->code === 'en') {
            return back()->with('error', 'Default English language cannot be disabled.');
        }

        $language->update(['is_active' => ! $language->is_active]);

        return back()->with('success', "Language [{$language->name}] status updated.");
    }

    public function updateTranslation(Request $request, ContentTranslation $translation)
    {
        $validated = $request->validate([
            'value' => ['nullable', 'string'],
        ]);

        $translation->update($validated);
        TranslationService::clearCache($translation->content_type, $translation->content_id, $translation->field);

        return back()->with('success', 'Translation updated successfully.');
    }

    public function autoTranslateMissing(Request $request)
    {
        $targetLang = $request->input('target_lang');
        $text = $request->input('text');
        $source = $request->input('source', 'en');

        $translated = TranslationService::autoTranslate($text, $source, $targetLang);

        return response()->json([
            'translated' => $translated,
        ]);
    }
}
