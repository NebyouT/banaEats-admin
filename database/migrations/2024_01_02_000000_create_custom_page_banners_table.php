<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomPageBannersTable extends Migration
{
    public function up()
    {
        Schema::create('custom_page_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image');
            // 'square' = 1:1 ratio  |  'wide' = 5:1 ratio
            $table->enum('type', ['square', 'wide'])->default('wide');
            $table->json('page_ids')->nullable();   // linked CustomPage IDs
            $table->boolean('status')->default(1);  // 1 = active, 0 = inactive
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('custom_page_banners');
    }
}
