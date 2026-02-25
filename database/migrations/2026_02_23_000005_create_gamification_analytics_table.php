<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gamification_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained('gamification_games')->onDelete('cascade');
            $table->date('date');
            $table->integer('total_plays')->default(0);
            $table->integer('total_winners')->default(0);
            $table->integer('total_claimed')->default(0);
            $table->integer('unique_players')->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0); // % who used prize
            $table->json('prize_distribution')->nullable(); // Count per prize type
            $table->timestamps();
            
            $table->unique(['game_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gamification_analytics');
    }
};
