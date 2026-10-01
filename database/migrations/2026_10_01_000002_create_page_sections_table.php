<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->string('page', 60);       // e.g. home, about, contact, products
            $table->string('section', 80);    // e.g. hero, cta, intro, story, vision
            $table->string('field', 80);      // e.g. title, description, image, btn_text, btn_url
            $table->longText('value')->nullable();  // text/JSON value
            $table->foreignId('media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->timestamps();

            $table->unique(['page', 'section', 'field']);
            $table->index(['page', 'section']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_sections');
    }
};
