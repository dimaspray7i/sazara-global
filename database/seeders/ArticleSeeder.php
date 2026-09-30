<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            ['Why Indonesian Palm Broom Is Gaining Global Demand',
             'Natural, durable, and sustainable: how palm broom became a rising export commodity.',
             "Indonesian palm broom is produced from natural palm fibers, making it a sustainable alternative to synthetic cleaning tools. Global buyers increasingly value its durability for household, commercial, agricultural, and outdoor applications.\n\nSazara Global Trade sources palm broom from trusted producers and coordinates quality, documentation, and shipment to ensure a reliable supply for international markets.",
             'Mengapa Palm Broom Indonesia Semakin Diminati Pasar Global',
             'Alami, tahan lama, dan berkelanjutan: bagaimana palm broom menjadi komoditas ekspor yang naik daun.',
             "Palm broom Indonesia diproduksi dari serat palma alami, menjadikannya alternatif berkelanjutan bagi alat pembersih sintetis. Pembeli global semakin menghargai daya tahannya untuk aplikasi rumah tangga, komersial, pertanian, dan luar ruang.\n\nSazara Global Trade sourcing palm broom dari produsen terpercaya serta mengoordinasikan kualitas, dokumentasi, dan pengiriman guna menjamin pasokan yang andal bagi pasar internasional."],
            ['A Practical Guide to Indonesian Coffee Origins and Grades',
             'Understanding origin, grade, and processing method before placing your first order.',
             "Indonesian coffee offers diverse profiles shaped by origin, altitude, and processing method. Buyers should define grade, moisture content, and cup quality expectations early in the negotiation.\n\nOur team assists buyers in matching specifications with the right origin, from Sumatra to Java and Sulawesi, ensuring transparency throughout the transaction.",
             'Panduan Praktis Origin dan Grade Kopi Indonesia',
             'Memahami origin, grade, dan metode proses sebelum menempatkan pesanan pertama Anda.',
             "Kopi Indonesia menawarkan profil rasa yang beragam, dibentuk oleh origin, ketinggian, dan metode proses. Pembeli perlu menetapkan grade, kadar air, dan harapan cup quality sejak awal negosiasi.\n\nTim kami mendampingi pembeli mencocokkan spesifikasi dengan origin yang tepat, dari Sumatra hingga Java dan Sulawesi, dengan transparansi sepanjang transaksi."],
            ['From Source to Shipment: How Commodity Export Coordination Works',
             'A look at our four-stage business flow: source, Sazara, export, global buyer.',
             "Successful commodity trade requires coordination across the supply chain: sourcing from producers and farmers, commercial negotiation, export documentation, quality coordination, and logistics.\n\nSazara's role is to ensure commercial requirements between suppliers and buyers are properly coordinated throughout the transaction, from first inquiry to final shipment.",
             'Dari Sumber ke Pengiriman: Bagaimana Koordinasi Ekspor Komoditas Bekerja',
             'Melihat alur bisnis empat tahap kami: source, Sazara, export, global buyer.',
             "Perdagangan komoditas yang sukses membutuhkan koordinasi di seluruh rantai pasok: sourcing dari produsen dan petani, negosiasi komersial, dokumentasi ekspor, koordinasi kualitas, hingga logistik.\n\nPeran Sazara adalah memastikan persyaratan komersial antara pemasok dan pembeli terkoordinasi dengan baik sepanjang transaksi, dari inquiry pertama hingga pengiriman akhir."],
        ];
        foreach ($articles as $i => [$title, $excerpt, $body, $titleId, $excerptId, $bodyId]) {
            Article::updateOrCreate(['slug' => Str::slug($title)], [
                'title' => $title, 'excerpt' => $excerpt, 'body' => $body,
                'title_id' => $titleId, 'excerpt_id' => $excerptId, 'body_id' => $bodyId,
                'status' => 'published', 'published_at' => now()->subDays($i * 7),
            ]);
        }
    }
}