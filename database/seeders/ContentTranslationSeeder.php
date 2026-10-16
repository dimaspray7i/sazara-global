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
        // 3. PageSections: Seed Indonesian translations from UI dictionary or value_id
        // ------------------------------------------------------------------
        $pageSectionTranslations = [
            'home.hero.eyebrow'      => 'PT Sazara Global Trade · Medan, Indonesia',
            'home.hero.title'        => 'Komoditas Indonesia.',
            'home.hero.title2'       => 'Koneksi Global.',
            'home.hero.description'  => 'Tumbuh di Indonesia. Digerakkan oleh Peluang Global. Kami melakukan sourcing, perdagangan, dan fasilitasi pasokan komoditas Indonesia untuk pasar domestik maupun internasional.',
            'home.hero.btn_text'     => 'Lihat Produk',
            'home.flow.title'        => 'Alur Bisnis Kami',
            'home.flow.subtitle'     => 'Sazara beroperasi di seluruh rantai pasok komoditas, menghubungkan sumber produksi dengan pasar.',
            'home.why.title'         => 'Mengapa Bermitra dengan Kami?',
            'home.cta.title'         => 'Mari Bangun Peluang Global Bersama.',
            'home.cta.description'   => 'Kami siap menjajaki peluang dengan importir, distributor, wholesaler, produsen, dan mitra bisnis yang mencari sourcing komoditas Indonesia yang andal.',

            'about.intro.heading'    => 'Menghubungkan Komoditas Indonesia dengan Pasar Global',
            'about.intro.paragraph1' => 'Berdiri pada 5 Agustus 2026, PT Sazara Global Trade adalah perusahaan perdagangan dan ekspor komoditas Indonesia yang berkedudukan di Medan, Sumatera Utara, Indonesia. Kami berfokus pada sourcing, perdagangan, dan fasilitasi pasokan komoditas Indonesia untuk pasar domestik maupun internasional.',
            'about.intro.paragraph2' => 'Portofolio komoditas kami meliputi Palm Broom, Crude Palm Oil (CPO), Coffee, Cloves, Cinnamon, Vanilla, Areca Nut, serta komoditas lain sesuai permintaan pasar dan spesifikasi pembeli.',
            'about.intro.paragraph3' => 'Dengan komitmen pada kualitas, keandalan, dan kemitraan jangka panjang, Sazara Global Trade bertujuan menjadi mitra bisnis terpercaya yang menghubungkan beragam sumber daya komoditas Indonesia dengan peluang di pasar global.',

            'about.story.title'      => 'Cerita Kami',
            'about.story.paragraph1' => 'Indonesia memiliki sumber daya alam yang melimpah serta ekosistem pertanian dan perkebunan yang kuat.',
            'about.story.paragraph2' => 'PT Sazara Global Trade didirikan untuk menangkap peluang tersebut dengan menciptakan jembatan profesional antara pemasok komoditas Indonesia dan pembeli di pasar internasional.',
            'about.story.paragraph3' => 'Dari sourcing dan koordinasi pemasok hingga negosiasi komersial, dokumentasi, dan koordinasi pengiriman, kami berupaya memberikan pengalaman perdagangan yang mulus bagi mitra bisnis kami.',
            'about.story.ambition'   => 'Ambisi kami sederhana:',
            'about.story.tagline'    => 'Tumbuh di Indonesia. Digerakkan oleh Peluang Global.',

            'about.mission.vision_title'  => 'Visi Kami',
            'about.mission.vision'        => 'Menjadi perusahaan perdagangan komoditas Indonesia yang terpercaya, yang menghubungkan sumber daya Indonesia dengan pasar global.',
            'about.mission.mission_title' => 'Misi Kami',
            'about.mission.mission'       => 'Membuat komoditas Indonesia lebih mudah diakses oleh pembeli global melalui kemitraan perdagangan yang andal dan profesional.',
            'about.values.title'          => 'Nilai-Nilai Kami',
            'about.cta.title'             => 'Mari Bangun Peluang Global Bersama.',
            'about.cta.description'       => 'Kami siap menjajaki peluang dengan importir, distributor, wholesaler, produsen, dan mitra bisnis yang mencari sourcing komoditas Indonesia yang andal.',

            'products.header.title'    => 'Komoditas Kami',
            'products.header.subtitle' => 'Portofolio beragam komoditas Indonesia, dipasok sesuai permintaan pasar dan spesifikasi pembeli.',
            'articles.header.title'    => 'Artikel & Wawasan',
            'articles.header.subtitle' => 'Wawasan pasar dan panduan praktis seputar perdagangan komoditas Indonesia.',
            'gallery.header.title'     => 'Galeri',
            'gallery.header.subtitle'  => 'Dokumentasi operasional kegiatan sourcing, perkebunan, pemanenan, pengemasan, dan ekspor global kami.',

            'contact.header.title'    => 'Mari Bangun Peluang Global Bersama.',
            'contact.header.subtitle' => 'Kami siap menjajaki peluang dengan importir, distributor, wholesaler, produsen, dan mitra bisnis yang mencari sourcing komoditas Indonesia yang andal.',
        ];

        foreach ($pageSectionTranslations as $contentId => $idValue) {
            $parts = explode('.', $contentId);
            $field = end($parts);

            $exists = ContentTranslation::where('content_type', 'page_section')
                ->where('content_id', $contentId)
                ->where('field', $field)
                ->where('language', 'id')
                ->exists();

            if (! $exists) {
                ContentTranslation::set('page_section', $contentId, $field, 'id', $idValue);
                $inserted++;
            }
        }

        $this->command->info("ContentTranslationSeeder: {$inserted} Indonesian rows inserted (existing rows preserved).");
        $this->command->line('  Note: For other languages (zh, ja, ko, ar, etc.) run:');
        $this->command->line('        php artisan translations:seed');
        $this->command->line('  Or use Admin Panel → Languages & Translations → Generate.');
    }
}
