<?php

namespace Database\Seeders;

use App\Models\Media;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure storage directory exists
        Storage::disk('public')->makeDirectory('media');

        $sources = [
            public_path('images/commodities') => 'Commodity',
            public_path('images/articles')    => 'Article',
            public_path('images/team')        => 'Team',
        ];

        foreach ($sources as $folder => $category) {
            if (! File::isDirectory($folder)) continue;

            $files = File::files($folder);
            foreach ($files as $file) {
                $filename = $file->getFilename();
                $targetPath = 'media/' . $filename;

                // Copy to storage/app/public/media/
                $fullDest = storage_path('app/public/' . $targetPath);
                if (! File::exists($fullDest)) {
                    File::copy($file->getPathname(), $fullDest);
                }

                Media::updateOrCreate(
                    ['filename' => $filename],
                    [
                        'original_name' => $filename,
                        'mime_type'     => File::mimeType($file->getPathname()) ?: 'image/jpeg',
                        'size'          => $file->getSize(),
                        'path'          => $targetPath,
                        'alt'           => ucwords(str_replace(['-', '_', '.jpg', '.png'], ' ', $filename)),
                    ]
                );
            }
        }

        // Hero and Logo
        foreach (['hero.jpg' => 'Sazara Hero', 'logo.jpg' => 'Sazara Logo'] as $img => $alt) {
            $path = public_path('images/' . $img);
            if (File::exists($path)) {
                $targetPath = 'media/' . $img;
                $fullDest = storage_path('app/public/' . $targetPath);
                if (! File::exists($fullDest)) {
                    File::copy($path, $fullDest);
                }
                Media::updateOrCreate(
                    ['filename' => $img],
                    [
                        'original_name' => $img,
                        'mime_type'     => File::mimeType($path) ?: 'image/jpeg',
                        'size'          => File::size($path),
                        'path'          => $targetPath,
                        'alt'           => $alt,
                    ]
                );
            }
        }
    }
}
