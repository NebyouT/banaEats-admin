<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gamification_game_plays', function (Blueprint $table) {
            $table->enum('prize_status', ['locked', 'unlocked', 'applied', 'expired'])->default('locked')->after('is_claimed');
            $table->timestamp('unlocked_at')->nullable()->after('prize_status');
            $table->timestamp('applied_at')->nullable()->after('unlocked_at');
            $table->unsignedBigInteger('applied_to_order_id')->nullable()->after('applied_at');
            $table->json('applied_details')->nullable()->after('applied_to_order_id');
        });

        // Add extra prize control fields
        Schema::table('gamification_prizes', function (Blueprint $table) {
            $table->integer('max_delivery_distance_km')->nullable()->after('min_order_amount');
            $table->integer('min_delivery_time_gap_minutes')->nullable()->after('max_delivery_distance_km');
            $table->integer('max_delivery_time_gap_minutes')->nullable()->after('min_delivery_time_gap_minutes');
            $table->json('valid_order_types')->nullable()->after('max_delivery_time_gap_minutes'); // delivery, takeaway, dine_in
            $table->json('valid_payment_methods')->nullable()->after('valid_order_types');
            $table->decimal('max_discount_amount', 10, 2)->nullable()->after('valid_payment_methods');
            $table->json('valid_food_ids')->nullable()->after('max_discount_amount');
            $table->json('valid_category_ids')->nullable()->after('valid_food_ids');
            $table->json('valid_cuisine_ids')->nullable()->after('valid_category_ids');
            $table->integer('min_cart_items')->nullable()->after('valid_cuisine_ids');
            $table->string('schedule_type')->nullable()->after('min_cart_items'); // all_day, specific_time
            $table->time('valid_from_time')->nullable()->after('schedule_type');
            $table->time('valid_until_time')->nullable()->after('valid_from_time');
            $table->json('valid_days')->nullable()->after('valid_until_time'); // [0,1,2,3,4,5,6] Mon-Sun
            $table->boolean('new_customer_only')->default(false)->after('valid_days');
            $table->integer('min_order_count')->nullable()->after('new_customer_only');
        });
    }

    public function down(): void
    {
        Schema::table('gamification_game_plays', function (Blueprint $table) {
            $table->dropColumn(['prize_status', 'unlocked_at', 'applied_at', 'applied_to_order_id', 'applied_details']);
        });

        Schema::table('gamification_prizes', function (Blueprint $table) {
            $table->dropColumn([
                'max_delivery_distance_km', 'min_delivery_time_gap_minutes', 'max_delivery_time_gap_minutes',
                'valid_order_types', 'valid_payment_methods', 'max_discount_amount',
                'valid_food_ids', 'valid_category_ids', 'valid_cuisine_ids',
                'min_cart_items', 'schedule_type', 'valid_from_time', 'valid_until_time',
                'valid_days', 'new_customer_only', 'min_order_count',
            ]);
        });
    }
};
