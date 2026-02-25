<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gamification_banners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained('gamification_games')->cascadeOnDelete();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('image')->nullable();
            $table->string('image_storage')->default('public');
            $table->string('background_color')->default('#8DC63F');
            $table->string('text_color')->default('#FFFFFF');
            $table->string('button_text')->default('Play Now');
            $table->string('button_color')->default('#F5D800');
            $table->string('placement')->default('home'); // home, restaurant, checkout, cart
            $table->integer('priority')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->json('zone_ids')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gamification_banners');
    }
};
