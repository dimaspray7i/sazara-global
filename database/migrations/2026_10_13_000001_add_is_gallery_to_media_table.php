<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add is_gallery flag to media table.
 *
 * is_public  = visible in Media Library (usable everywhere)
 * is_gallery = explicitly selected to appear in the Public Gallery
 *
 * Default false: uploaded media does NOT appear in Gallery unless
 * admin explicitly marks it. This prevents product photos, team photos,
 * hero images, logos, and article illustrations from auto-appearing in Gallery.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->boolean('is_gallery')->default(false)->after('is_public');
        });
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn('is_gallery');
        });
    }
};
