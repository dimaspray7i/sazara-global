<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LanguageSeeder::class,           // 1. Languages first (translations depend on it)
            AdminSeeder::class,              // 2. Admin user
            ProductSeeder::class,            // 3. Products
            ProductImageSeeder::class,       // 4. Assign product images (was missing!)
            ArticleSeeder::class,            // 5. Articles
            PageSectionSeeder::class,        // 6. CMS page sections
            MediaSeeder::class,              // 7. Media (scans physical files)
            ContentTranslationSeeder::class, // 8. Indonesian translations from existing _id columns
        ]);
    }
}