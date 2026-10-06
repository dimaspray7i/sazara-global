<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->string('title')->nullable()->after('alt');
            $table->string('title_id')->nullable()->after('title');
            $table->text('caption')->nullable()->after('title_id');
            $table->text('caption_id')->nullable()->after('caption');
            $table->string('category', 60)->default('commodities')->after('caption_id');
            $table->boolean('is_public')->default(true)->after('category');
            $table->integer('sort_order')->default(0)->after('is_public');
        });
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn(['title', 'title_id', 'caption', 'caption_id', 'category', 'is_public', 'sort_order']);
        });
    }
};
