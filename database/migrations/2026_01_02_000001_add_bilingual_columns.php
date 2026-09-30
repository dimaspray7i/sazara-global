<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('name_id')->nullable()->after('name');
            $table->text('description_id')->nullable()->after('description');
            $table->text('specification_id')->nullable()->after('specification');
        });
        Schema::table('articles', function (Blueprint $table) {
            $table->string('title_id')->nullable()->after('title');
            $table->text('excerpt_id')->nullable()->after('excerpt');
            $table->longText('body_id')->nullable()->after('body');
        });
    }

    public function down(): void
    {
        Schema::table('products', fn (Blueprint $table) => $table->dropColumn(['name_id', 'description_id', 'specification_id']));
        Schema::table('articles', fn (Blueprint $table) => $table->dropColumn(['title_id', 'excerpt_id', 'body_id']));
    }
};