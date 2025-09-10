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
        //add pending_amount, invoices no, pending_amount,  in invoices table
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('invoice_no')->nullable();
            $table->decimal('pending_amount', 10, 2)->default(0);
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
