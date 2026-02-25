<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gamification_prizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained('gamification_games')->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['discount_percentage', 'discount_fixed', 'free_delivery', 'loyalty_points', 'wallet_credit', 'free_item', 'mystery'])->default('discount_percentage');
            $table->decimal('value', 10, 2)->default(0); // Discount amount, points, credit amount
            $table->text('description')->nullable();
            $table->decimal('probability', 5, 2)->default(10.00); // Percentage chance (0-100)
            $table->integer('total_quantity')->nullable(); // NULL = unlimited
            $table->integer('remaining_quantity')->nullable();
            $table->boolean('allow_multiple_wins')->default(0);
            $table->integer('expiry_days')->default(7); // Days until prize expires
            $table->decimal('min_order_amount', 10, 2)->nullable(); // Minimum order to use prize
            $table->json('restaurant_ids')->nullable(); // Specific restaurants, NULL = all
            $table->json('zone_ids')->nullable(); // Specific zones, NULL = all
            $table->string('image')->nullable();
            $table->string('color')->default('#8DC63F'); // Display color on wheel/card
            $table->boolean('status')->default(1);
            $table->integer('position')->default(0); // Order on wheel
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gamification_prizes');
    }
};
