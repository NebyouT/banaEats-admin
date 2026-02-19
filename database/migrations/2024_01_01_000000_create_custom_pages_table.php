<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomPagesTable extends Migration
{
    public function up()
    {
        Schema::create('custom_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('subtitle')->nullable();
            $table->string('promotional_text')->nullable();
            $table->string('background_image')->nullable();
            $table->string('background_color', 20)->nullable()->default('#ffffff');
            $table->json('product_ids')->nullable();
            $table->json('restaurant_ids')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('custom_pages');
    }
}
