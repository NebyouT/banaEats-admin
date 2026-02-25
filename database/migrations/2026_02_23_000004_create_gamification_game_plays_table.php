<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gamification_game_plays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained('gamification_games')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('prize_id')->nullable()->constrained('gamification_prizes')->onDelete('set null');
            $table->boolean('is_winner')->default(0);
            $table->string('prize_code')->nullable()->unique(); // Unique code for claiming prize
            $table->boolean('is_claimed')->default(0);
            $table->dateTime('claimed_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null'); // Order where prize was used
            $table->json('game_data')->nullable(); // Store spin result, scratch positions, etc.
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'game_id', 'created_at']);
            $table->index(['prize_code']);
            $table->index(['is_claimed', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gamification_game_plays');
    }
};
