<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('hero_section', function (Blueprint $table) {
            $table->string('background_image_file_id')->nullable();
        });
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->string('image_file_id', 255)->nullable();
        });
        Schema::table('about_page', function (Blueprint $table) {
            $table->string('hero_image_file_id', 255)->nullable();
            $table->string('commitment_image_file_id', 255)->nullable();
        });
        Schema::table('certificates', function (Blueprint $table) {
            $table->string('logo_file_id', 255)->nullable();
        });
        Schema::table('gallery_metadata', function (Blueprint $table) {
            $table->string('background_image_file_id', 255)->nullable();
        });
        Schema::table('gallery_images', function (Blueprint $table) {
            $table->string('image_path_file_id', 255)->nullable();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->string('image_file_id', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hero_section', function (Blueprint $table) {
            $table->dropColumn('background_image_file_id');
        });
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn('image_file_id');
        });
        Schema::table('about_page', function (Blueprint $table) {
            $table->dropColumn(['hero_image_file_id', 'commitment_image_file_id']);
        });
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn('logo_file_id');
        });
        Schema::table('gallery_metadata', function (Blueprint $table) {
            $table->dropColumn('background_image_file_id');
        });
        Schema::table('gallery_images', function (Blueprint $table) {
            $table->dropColumn('image_path_file_id');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('image_file_id');
        });
    }
};
