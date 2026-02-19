<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCustomPageBannersAndPagesMedia extends Migration
{
    public function up()
    {
        // ── custom_page_banners ──────────────────────────────────────────────────
        Schema::table('custom_page_banners', function (Blueprint $table) {
            // Replace page_ids JSON array with a single FK
            $table->dropColumn('page_ids');
            $table->unsignedBigInteger('page_id')->nullable()->after('type');
            // Track whether the uploaded file is an image, gif, or video
            $table->enum('media_type', ['image', 'gif', 'video'])->default('image')->after('image');
        });

        // ── custom_pages ─────────────────────────────────────────────────────────
        Schema::table('custom_pages', function (Blueprint $table) {
            // Track whether background_image is actually an image, gif, or video
            $table->enum('background_media_type', ['image', 'gif', 'video'])
                  ->default('image')
                  ->after('background_image');
        });
    }

    public function down()
    {
        Schema::table('custom_page_banners', function (Blueprint $table) {
            $table->dropColumn(['page_id', 'media_type']);
            $table->json('page_ids')->nullable();
        });

        Schema::table('custom_pages', function (Blueprint $table) {
            $table->dropColumn('background_media_type');
        });
    }
}
