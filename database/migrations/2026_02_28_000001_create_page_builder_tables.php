<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePageBuilderTables extends Migration
{
    public function up()
    {
        // Main pages table
        Schema::create('builder_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('page_type')->default('custom'); // custom, promotion, category, etc.
            $table->json('settings')->nullable(); // Global page settings (background, fonts, etc.)
            $table->boolean('status')->default(1);
            $table->boolean('is_published')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        // Sections within a page (rows/containers)
        Schema::create('builder_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('builder_pages')->onDelete('cascade');
            $table->string('section_type'); // hero, products_grid, restaurants_list, banner, text, etc.
            $table->string('name')->nullable();
            $table->integer('order')->default(0);
            $table->json('settings')->nullable(); // Section-specific settings (padding, margin, background, etc.)
            $table->json('style')->nullable(); // CSS styles
            $table->boolean('is_visible')->default(1);
            $table->timestamps();
        });

        // Components within sections
        Schema::create('builder_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('builder_sections')->onDelete('cascade');
            $table->string('component_type'); // button, image, text, product_card, restaurant_card, etc.
            $table->integer('order')->default(0);
            $table->integer('column_span')->default(12); // Grid column span (1-12)
            $table->json('content')->nullable(); // Component content (text, urls, etc.)
            $table->json('settings')->nullable(); // Component settings
            $table->json('style')->nullable(); // CSS styles
            $table->json('data_source')->nullable(); // For dynamic data (product_ids, restaurant_ids, etc.)
            $table->json('action')->nullable(); // Click action (navigate, link, etc.)
            $table->boolean('is_visible')->default(1);
            $table->timestamps();
        });

        // Pre-built templates
        Schema::create('builder_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->default('general'); // promotion, food, restaurant, etc.
            $table->string('thumbnail')->nullable();
            $table->json('structure')->nullable(); // Full page structure JSON
            $table->boolean('is_system')->default(0); // System templates can't be deleted
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        // Page analytics
        Schema::create('builder_page_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('builder_pages')->onDelete('cascade');
            $table->string('user_id')->nullable();
            $table->string('session_id')->nullable();
            $table->string('device_type')->nullable();
            $table->string('referrer')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('builder_page_views');
        Schema::dropIfExists('builder_templates');
        Schema::dropIfExists('builder_components');
        Schema::dropIfExists('builder_sections');
        Schema::dropIfExists('builder_pages');
    }
}
