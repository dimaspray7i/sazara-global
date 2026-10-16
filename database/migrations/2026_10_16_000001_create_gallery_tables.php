<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Gallery categories (dynamic, managed from admin)
        Schema::create('gallery_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 60)->unique();          // machine key: 'logistics', 'plantation'
            $table->string('name');                        // EN label
            $table->string('name_id')->nullable();         // ID label
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Galleries (single media OR group)
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->string('title');                        // EN title
            $table->string('title_id')->nullable();         // ID title
            $table->text('description')->nullable();        // EN description
            $table->text('description_id')->nullable();     // ID description
            $table->string('category_slug', 60)->nullable();// FK to gallery_categories.slug
            $table->enum('type', ['single', 'group'])->default('single');
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->unsignedBigInteger('cover_media_id')->nullable(); // which media is the cover
            $table->integer('sort_order')->default(0);
            $table->integer('media_count')->default(0);     // denormalized for quick display
            $table->timestamps();

            $table->index('status');
            $table->index('category_slug');
            $table->index('sort_order');
        });

        // Pivot: gallery ↔ media (ordered)
        Schema::create('gallery_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gallery_id');
            $table->unsignedBigInteger('media_id');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['gallery_id', 'media_id']);
            $table->index('gallery_id');
            $table->index('media_id');

            $table->foreign('gallery_id')->references('id')->on('galleries')->onDelete('cascade');
            $table->foreign('media_id')->references('id')->on('media')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_media');
        Schema::dropIfExists('galleries');
        Schema::dropIfExists('gallery_categories');
    }
};
