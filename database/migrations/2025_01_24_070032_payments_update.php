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
        //
        //drop if exists payments table
        Schema::dropIfExists('payments');
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->string('payment_method');
            $table->decimal('amount', 10, 2);
            $table->decimal('amount_received', 10, 2)->nullable();
            $table->string('transaction_id')->unique();
            $table->string('pg_result')->nullable();
            $table->string('track_id')->nullable();
            $table->text('response')->nullable();
            $table->json('pg_response_json')->nullable();
            $table->dateTime('payment_date');
            $table->enum('status', ['successful', 'failed', 'pending']);
            $table->string('order_id')->nullable();
            $table->timestamps();
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('payments');
    }
};
