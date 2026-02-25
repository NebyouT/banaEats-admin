<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gamification_eligibility_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained('gamification_games')->onDelete('cascade');
            $table->string('rule_type'); // 'order_count', 'new_user', 'inactive_user', 'total_spent', 'zone', 'time_of_day', 'day_of_week', 'last_order_days'
            $table->string('operator'); // '>=', '<=', '=', '!=', 'in', 'not_in', 'between'
            $table->json('value'); // The value to compare against
            $table->boolean('is_required')->default(1); // AND vs OR logic
            $table->integer('priority')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gamification_eligibility_rules');
    }
};
