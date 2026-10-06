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
        $langs = ['zh', 'ja', 'ko', 'ar', 'es', 'fr', 'de', 'pt', 'it', 'ru', 'hi', 'th', 'vi'];

        $keys = [
            'ui.nav.home'        => 'Home',
            'ui.nav.product'     => 'Product',
            'ui.nav.article'     => 'Article',
            'ui.nav.about'       => 'About Us',
            'ui.nav.contact'     => 'Contact Us',
            'ui.nav.gallery'     => 'Gallery',
            'ui.common.get_offer'    => 'Get Offer',
            'ui.common.learn_more'   => 'Learn More',
            'ui.common.read_more'    => 'Read More',
            'ui.common.chat_wa'      => 'Chat on WhatsApp',
            'ui.common.get_offer_wa' => 'Get Offer via WhatsApp',
            'ui.common.send'         => 'Send Message',
            'ui.common.view_products'=> 'View Products',
            'ui.common.specification'=> 'Specification',
            'ui.common.back_products'=> 'Back to Products',
            'ui.common.back_articles'=> 'Back to Articles',
            'ui.footer.tagline'  => 'Indonesian Commodities. Global Connections.',
            'ui.footer.tagline2' => 'Trusted Partner, Global Impact.',
            'ui.footer.pages'    => 'Pages',
            'ui.footer.commodities' => 'Commodities',
            'ui.footer.contact'  => 'Contact',
            'ui.footer.rights'   => 'All rights reserved.',
            'ui.footer.gallery'  => 'Gallery',
            'ui.home.title1'     => 'Indonesian Commodities.',
            'ui.home.title2'     => 'Global Connections.',
            'ui.home.eyebrow'    => 'PT Sazara Global Trade · Medan, Indonesia',
            'ui.home.flow_title' => 'Our Business Flow',
            'ui.home.why_title'  => 'Why Partner With Us?',
            'ui.home.featured_title' => 'Featured Commodities',
            'ui.home.cta_title'  => "Let's Build Global Opportunities Together.",
            'ui.products.title'  => 'Our Commodities',
            'ui.articles.title'  => 'Articles & Insights',
            'ui.gallery.title'   => 'Gallery',
            'ui.gallery.all'     => 'All',
            'ui.gallery.commodities' => 'Commodities',
            'ui.contact.title'   => "Let's Build Global Opportunities Together.",
            'ui.about.heading'   => 'Connecting Indonesian Commodities with Global Markets',
            'ui.about.vision_title'  => 'Our Vision',
            'ui.about.mission_title' => 'Our Mission',
            'ui.about.values_title'  => 'Our Core Values',
            'ui.about.story_title'   => 'Our Story',
            'ui.about.team_title'    => 'Our Team',
            'ui.search.placeholder'  => 'Search products, articles, gallery...',
            'ui.search.title'        => 'Search Results',
        ];

        $total = count($langs) * count($keys);
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($langs as $lang) {
            $this->line('');
            $this->info("Seeding lang: {$lang}");
            foreach ($keys as $key => $en) {
                try {
                    TranslationService::get('ui', $key, 'text', $en, $lang);
                    usleep(200000); // 200ms to avoid API rate limiting
                } catch (\Throwable $e) {
                    $this->warn("  Failed [{$lang}] {$key}: " . $e->getMessage());
                }
                $bar->advance();
            }
        }

        $bar->finish();
        $this->line('');
        $this->info('Translation seeding complete!');

        return self::SUCCESS;
    }
}