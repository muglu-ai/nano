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
        //update invoice table with new columns for discount_per, discount, gst, processing charges, price, and total_final_price
        Schema::table('invoices', function (Blueprint $table) {
            $table->float('discount_per')->nullable();
            $table->float('discount')->nullable();
            $table->float('gst')->nullable();
            $table->float('processing_charges')->nullable();
            $table->float('price')->nullable();
            $table->float('total_final_price')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
