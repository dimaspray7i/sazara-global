<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        $languages = [
            ['code' => 'id', 'name' => 'Indonesian', 'native_name' => 'Bahasa Indonesia', 'flag' => '🇮🇩', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 1],
            ['code' => 'en', 'name' => 'English',    'native_name' => 'English',          'flag' => '🇬🇧', 'direction' => 'ltr', 'is_active' => true, 'is_default' => true,  'sort_order' => 2],
            ['code' => 'zh', 'name' => 'Chinese',    'native_name' => '中文',             'flag' => '🇨🇳', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 3],
            ['code' => 'ja', 'name' => 'Japanese',   'native_name' => '日本語',           'flag' => '🇯🇵', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 4],
            ['code' => 'ar', 'name' => 'Arabic',     'native_name' => 'العربية',          'flag' => '🇸🇦', 'direction' => 'rtl', 'is_active' => true, 'is_default' => false, 'sort_order' => 5],
            ['code' => 'ko', 'name' => 'Korean',     'native_name' => '한국어',           'flag' => '🇰🇷', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 6],
            ['code' => 'es', 'name' => 'Spanish',    'native_name' => 'Español',          'flag' => '🇪🇸', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 7],
            ['code' => 'fr', 'name' => 'French',     'native_name' => 'Français',         'flag' => '🇫🇷', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 8],
            ['code' => 'de', 'name' => 'German',     'native_name' => 'Deutsch',          'flag' => '🇩🇪', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 9],
            ['code' => 'pt', 'name' => 'Portuguese', 'native_name' => 'Português',        'flag' => '🇵🇹', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 10],
            ['code' => 'it', 'name' => 'Italian',    'native_name' => 'Italiano',         'flag' => '🇮🇹', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 11],
            ['code' => 'ru', 'name' => 'Russian',    'native_name' => 'Русский',          'flag' => '🇷🇺', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 12],
            ['code' => 'hi', 'name' => 'Hindi',      'native_name' => 'हिन्दी',            'flag' => '🇮🇳', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 13],
            ['code' => 'th', 'name' => 'Thai',       'native_name' => 'ไทย',              'flag' => '🇹🇭', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 14],
            ['code' => 'vi', 'name' => 'Vietnamese', 'native_name' => 'Tiếng Việt',       'flag' => '🇻🇳', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 15],
        ];

        foreach ($languages as $lang) {
            Language::updateOrCreate(['code' => $lang['code']], $lang);
        }
    }
}
