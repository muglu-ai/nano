<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('extra_requirements', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->integer('days'); // Number of days for the item rental
            $table->decimal('price_for_expo', 10, 2); // Price per item
            $table->integer('image_quantity')->default(0); // Number of images for display
            $table->integer('available_quantity')->default(0); // Total available quantity
            $table->enum('status', ['available', 'out_of_stock'])->default('available');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('extra_requirements');
    }
};
