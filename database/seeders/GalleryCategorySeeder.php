<?php

namespace Database\Seeders;

use App\Models\GalleryCategory;
use Illuminate\Database\Seeder;

class GalleryCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['slug' => 'logistics',  'name' => 'Logistics & Shipping',    'name_id' => 'Logistik & Pengiriman',    'sort_order' => 1],
            ['slug' => 'plantation', 'name' => 'Plantation & Farms',       'name_id' => 'Perkebunan & Pertanian',   'sort_order' => 2],
            ['slug' => 'harvesting', 'name' => 'Harvesting',               'name_id' => 'Panen',                    'sort_order' => 3],
            ['slug' => 'packaging',  'name' => 'Quality & Packaging',      'name_id' => 'Kualitas & Pengemasan',    'sort_order' => 4],
            ['slug' => 'export',     'name' => 'Export & Loading',         'name_id' => 'Ekspor & Loading',         'sort_order' => 5],
            ['slug' => 'company',    'name' => 'Company & Field Visits',   'name_id' => 'Kegiatan Perusahaan',      'sort_order' => 6],
            ['slug' => 'warehouse',  'name' => 'Warehouse & Storage',      'name_id' => 'Gudang & Penyimpanan',     'sort_order' => 7],
            ['slug' => 'inspection', 'name' => 'Inspection & QC',          'name_id' => 'Inspeksi & Kontrol Mutu',  'sort_order' => 8],
        ];

        foreach ($categories as $cat) {
            GalleryCategory::updateOrCreate(
                ['slug' => $cat['slug']],
                array_merge($cat, ['is_active' => true])
            );
        }

        $this->command->info('GalleryCategorySeeder: ' . count($categories) . ' categories seeded.');
    }
}
