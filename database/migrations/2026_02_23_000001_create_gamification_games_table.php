<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gamification_games', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', ['spin_wheel', 'scratch_card', 'slot_machine', 'mystery_box', 'decision_roulette'])->default('spin_wheel');
            $table->text('description')->nullable();
            $table->json('config')->nullable(); // Game-specific configuration (colors, animations, etc.)
            $table->boolean('status')->default(1); // Active/Inactive
            $table->boolean('first_play_always_wins')->default(0);
            $table->integer('plays_per_day')->default(1);
            $table->integer('plays_per_week')->nullable();
            $table->integer('cooldown_minutes')->default(0); // Time between plays
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->integer('priority')->default(0); // Display order
            $table->string('background_image')->nullable();
            $table->string('button_text')->default('Play Now');
            $table->text('instructions')->nullable();
            $table->json('display_settings')->nullable(); // Colors, fonts, etc.
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gamification_games');
    }
};
