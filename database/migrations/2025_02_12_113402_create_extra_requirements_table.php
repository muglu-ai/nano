<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('extra_requirements', function (Blueprint $table) {
            $table->id();
            $table->string('item_code', 50);
            $table->string('item_name');
            $table->string('size_or_description')->nullable();
            $table->integer('days');
            $table->decimal('price_for_expo', 10, 2);
            $table->string('image')->default('0');
            $table->integer('available_quantity')->default(0);
            $table->enum('status', ['available', 'out_of_stock'])->default('available');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('extra_requirements');
    }
};
