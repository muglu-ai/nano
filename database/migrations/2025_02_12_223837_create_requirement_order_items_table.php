<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('requirement_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requirements_order_id')->constrained('requirements_orders')->onDelete('cascade');
            $table->foreignId('requirement_id')->constrained('extra_requirements')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('requirement_order_items');
    }
};
