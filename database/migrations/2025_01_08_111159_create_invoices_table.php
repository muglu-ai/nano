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
        if (!Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('application_id')->nullable()->constrained('applications');
                $table->unsignedBigInteger('sponsorship_id')->nullable(); // Foreign key will be added after sponsorships table exists
            $table->string('type')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('rate', 10, 2)->nullable();
            $table->decimal('int_amount_value', 10, 2)->nullable();
            $table->decimal('usd_rate', 10, 2)->nullable();
            $table->enum('currency', ['EUR', 'INR', 'USD']);
            $table->enum('payment_status', ['unpaid', 'credit', 'partial', 'paid', 'overdue']);
            $table->date('payment_due_date');
            $table->double('discount_per')->nullable();
            $table->double('discount')->nullable();
            $table->double('gst')->nullable();
            $table->integer('processing_chargesRate')->nullable();
            $table->double('processing_charges')->nullable();
            $table->double('price')->nullable();
            $table->decimal('partial_payment_percentage', 5, 2)->nullable();
            $table->double('total_final_price')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('pin_no', 50)->nullable();
            $table->decimal('pending_amount', 10, 2)->default(0.00);
            $table->string('application_no')->nullable();
            $table->string('sponsorship_no')->nullable();
            $table->decimal('amount_paid', 10, 2)->default(0.00);
            $table->string('co_exhibitorID', 100)->nullable();
            $table->json('remarks')->nullable();
            $table->double('tds_amount')->default(0);
            $table->text('tax_invoice')->nullable();
            $table->integer('surCharge')->default(0);
            $table->integer('surChargepercentage')->default(0);
            $table->boolean('surChargeRemove')->default(true);
            $table->text('surChargeReason')->nullable();
            $table->tinyInteger('removeProcessing')->default(0);
            $table->text('tdsReason')->nullable();
            $table->boolean('surChargeLock')->default(false);
            $table->boolean('refund')->default(false);
            $table->timestamps();

                // Indexes
                $table->index('application_id');
                $table->index('application_no');
                $table->index('sponsorship_no');
                $table->index('sponsorship_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
