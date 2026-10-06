<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('languages')) {
            Schema::create('languages', function (Blueprint $table) {
                $table->id();
                $table->string('code', 10)->unique(); // e.g. en, id, zh, ja, ar, es
                $table->string('name', 50);          // e.g. English, Indonesian, Chinese
                $table->string('native_name', 50);   // e.g. English, Bahasa Indonesia, 中文
                $table->string('flag', 10)->nullable(); // e.g. 🇬🇧, 🇮🇩, 🇨🇳
                $table->string('direction', 3)->default('ltr'); // 'ltr' or 'rtl'
                $table->boolean('is_active')->default(true);
                $table->boolean('is_default')->default(false);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('content_translations')) {
            Schema::create('content_translations', function (Blueprint $table) {
                $table->id();
                $table->string('content_type', 60); // e.g. 'ui', 'product', 'article', 'media', 'page_section'
                $table->string('content_id', 60);   // id or key string like 'nav.home'
                $table->string('field', 60);        // e.g. 'text', 'name', 'title', 'description'
                $table->string('language', 10);     // e.g. 'zh', 'ar', 'ja'
                $table->longText('value')->nullable();
                $table->timestamps();

                $table->unique(['content_type', 'content_id', 'field', 'language'], 'content_trans_unique');
                $table->index(['language', 'content_type']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('content_translations');
        Schema::dropIfExists('languages');
    }
};
