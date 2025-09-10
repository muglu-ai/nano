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

        Schema::table('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('application_id')->nullable();
            $table->unsignedBigInteger('sponsorship_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('pending_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->string('currency');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid', 'overdue']);
            $table->date('payment_due_date');
            $table->timestamps();
            $table->foreign('application_id')->references('id')->on('applications')->onDelete('set null');
            $table->foreign('sponsorship_id')->references('id')->on('sponsorships')->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
