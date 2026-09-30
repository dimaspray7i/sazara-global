<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['Palm Broom', 'Natural palm-based cleaning products suitable for household, commercial, agricultural, and outdoor applications.',
             'Produk pembersih berbasis serat palma alami yang cocok untuk aplikasi rumah tangga, komersial, pertanian, dan luar ruang.', true],
            ['Crude Palm Oil (CPO)', 'Indonesian crude palm oil supplied in line with international trade standards and buyer specifications.',
             'Crude palm oil Indonesia yang dipasok sesuai standar perdagangan internasional dan spesifikasi pembeli.', true],
            ['Coffee', 'Indonesian coffee sourced according to origin, grade, processing method, quality, and buyer specifications.',
             'Kopi Indonesia yang bersumber sesuai origin, grade, metode proses, kualitas, dan spesifikasi pembeli.', true],
            ['Clove', 'Indonesian cloves serving food, spice, manufacturing, and other international applications.',
             'Cengkeh Indonesia untuk aplikasi makanan, rempah, manufaktur, dan keperluan internasional lainnya.', false],
            ['Cinnamon', 'Indonesian cinnamon for food, beverage, spice, ingredient, and other commercial applications.',
             'Kayu manis Indonesia untuk aplikasi makanan, minuman, rempah, bahan baku, dan keperluan komersial lainnya.', false],
            ['Vanilla', 'Indonesian vanilla for food, beverage, flavouring, fragrance, and related industries.',
             'Vanili Indonesia untuk industri makanan, minuman, perisa, fragrans, dan industri terkait.', false],
            ['Areca Nut', 'Indonesian areca nut supplied according to international market requirements and buyer specifications.',
             'Pinang Indonesia yang dipasok sesuai persyaratan pasar internasional dan spesifikasi pembeli.', false],
            ['Other Commodities', 'Our sourcing capabilities extend beyond our core portfolio. If you have a specific Indonesian commodity requirement, talk to us.',
             'Kemampuan sourcing kami melampaui portofolio inti. Bila Anda memiliki kebutuhan komoditas Indonesia yang spesifik, hubungi kami.', false],
        ];
        foreach ($items as $i => [$name, $desc, $descId, $featured]) {
            Product::updateOrCreate(['slug' => Str::slug($name)], [
                'name' => $name, 'description' => $desc, 'description_id' => $descId,
                'is_featured' => $featured, 'is_active' => true, 'sort_order' => $i + 1,
            ]);
        }
    }
}