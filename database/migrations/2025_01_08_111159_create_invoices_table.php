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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->nullable()->constrained('applications');
            $table->foreignId('sponsorship_id')->nullable()->constrained('sponsorships');
            $table->decimal('amount', 10, 2);
            $table->enum('currency', ['EUR', 'INR']);
            $table->enum('payment_status', ['unpaid', 'partial', 'paid', 'overdue']);
            $table->date('payment_due_date');

            $table->timestamps();
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
