<?php

namespace Database\Seeders;

use App\Models\PageSection;
use Illuminate\Database\Seeder;

class PageSectionSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            // ---- HOME ----
            ['home', 'hero', 'eyebrow',      'PT Sazara Global Trade · Medan, Indonesia'],
            ['home', 'hero', 'title',         'Indonesian Commodities.'],
            ['home', 'hero', 'title2',        'Global Connections.'],
            ['home', 'hero', 'description',   'Built in Indonesia. Driven by Global Opportunity. We source, trade, and facilitate the supply of Indonesian commodities for domestic and international markets.'],
            ['home', 'hero', 'btn_text',      'View Products'],
            ['home', 'hero', 'btn_url',       '/products'],

            ['home', 'flow', 'title',    'Our Business Flow'],
            ['home', 'flow', 'subtitle', 'Sazara operates across the commodity supply chain, connecting source to market.'],

            ['home', 'why', 'title', 'Why Partner With Us?'],

            ['home', 'cta', 'title',       "Let's Build Global Opportunities Together."],
            ['home', 'cta', 'description', 'We are ready to explore opportunities with importers, distributors, wholesalers, manufacturers, and business partners looking for reliable commodity sourcing from Indonesia.'],

            // ---- ABOUT ----
            ['about', 'intro', 'heading',    'Connecting Indonesian Commodities with Global Markets'],
            ['about', 'intro', 'paragraph1', 'Established on 5 August 2026, PT Sazara Global Trade is an Indonesian commodity trading and export company headquartered in Medan, North Sumatra, Indonesia. We focus on sourcing, trading, and facilitating the supply of Indonesian commodities for domestic and international markets.'],
            ['about', 'intro', 'paragraph2', 'Our commodity portfolio includes Palm Broom, Crude Palm Oil (CPO), Coffee, Cloves, Cinnamon, Vanilla, Areca Nut, and other commodities according to market demand and buyer specifications.'],
            ['about', 'intro', 'paragraph3', "With a commitment to quality, reliability, and long-term partnership, Sazara Global Trade aims to become a trusted business partner connecting Indonesia's diverse commodity resources with opportunities in global markets."],

            ['about', 'story', 'title',      'Our Story'],
            ['about', 'story', 'paragraph1', 'Indonesia possesses abundant natural resources and a strong agricultural and plantation ecosystem.'],
            ['about', 'story', 'paragraph2', 'PT Sazara Global Trade was established to capture the opportunity by creating a professional bridge between Indonesian commodity suppliers and buyers in international markets.'],
            ['about', 'story', 'paragraph3', 'From sourcing and supplier coordination to commercial negotiation, documentation, and shipment coordination, we strive to provide a seamless trading experience for our business partners.'],
            ['about', 'story', 'ambition',   'Our ambition is simple:'],
            ['about', 'story', 'tagline',    'Built in Indonesia. Driven by Global Opportunity.'],

            ['about', 'mission', 'vision_title',   'Our Vision'],
            ['about', 'mission', 'vision',         "To become a trusted Indonesian commodity trading company connecting Indonesia's resources to global markets."],
            ['about', 'mission', 'mission_title',  'Our Mission'],
            ['about', 'mission', 'mission',        'To make Indonesian commodities more accessible to global buyers through reliable and professional trading partnerships.'],

            ['about', 'values', 'title', 'Our Core Values'],

            ['about', 'cta', 'title',       "Let's Build Global Opportunities Together."],
            ['about', 'cta', 'description', 'We are ready to explore opportunities with importers, distributors, wholesalers, manufacturers, and business partners looking for reliable commodity sourcing from Indonesia.'],

            // ---- PRODUCTS ----
            ['products', 'header', 'title',    'Our Commodities'],
            ['products', 'header', 'subtitle', 'A diverse portfolio of Indonesian commodities, supplied according to market demand and buyer specifications.'],

            // ---- ARTICLES ----
            ['articles', 'header', 'title',    'Articles & Insights'],
            ['articles', 'header', 'subtitle', 'Market insights and practical guides on Indonesian commodity trade.'],

            // ---- CONTACT ----
            ['contact', 'header', 'title',    "Let's Build Global Opportunities Together."],
            ['contact', 'header', 'subtitle', 'We are ready to explore opportunities with importers, distributors, wholesalers, manufacturers, and business partners looking for reliable commodity sourcing from Indonesia.'],
            ['contact', 'info',   'address',  'Medan, North Sumatra, Indonesia'],
            ['contact', 'info',   'email',    'contact@sazaraglobal.com'],
            ['contact', 'info',   'website',  'www.sazaraglobal.com'],

            // ---- GLOBAL SETTINGS ----
            ['settings', 'general', 'whatsapp_number',  '+62 812-6040-7208'],
            ['settings', 'general', 'whatsapp_message', 'Halo Sazara Global, saya ingin mengetahui lebih lanjut tentang layanan ekspor komoditas Anda.'],
            ['settings', 'general', 'contact_person',   'Afriansyah Munar'],
            ['settings', 'general', 'email',            'contact@sazaraglobal.com'],
            ['settings', 'general', 'phone',            '+62 812-6040-7208'],
            ['settings', 'general', 'address',          'Medan, North Sumatra, Indonesia'],
        ];

        foreach ($sections as [$page, $section, $field, $value]) {
            PageSection::updateOrCreate(
                ['page' => $page, 'section' => $section, 'field' => $field],
                ['value' => $value]
            );
        }
    }
}
