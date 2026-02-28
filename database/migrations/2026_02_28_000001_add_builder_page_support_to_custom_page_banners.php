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
        Schema::table('custom_page_banners', function (Blueprint $table) {
            $table->string('page_type')->default('custom')->after('page_id')->comment('custom or builder');
            $table->unsignedBigInteger('builder_page_id')->nullable()->after('page_type');
            
            $table->foreign('builder_page_id')->references('id')->on('builder_pages')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_page_banners', function (Blueprint $table) {
            $table->dropForeign(['builder_page_id']);
            $table->dropColumn(['page_type', 'builder_page_id']);
        });
    }
};
