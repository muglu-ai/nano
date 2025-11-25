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

        Schema::table('sponsorships', function (Blueprint $table) {
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->string('payment_method');
            $table->decimal('amount', 10, 2);
            $table->decimal('amount_paid', 10, 2)->nullable();
            $table->decimal('amount_received', 10, 2)->nullable();
            $table->string('transaction_id');
            $table->string('pg_result')->nullable();
            $table->string('track_id')->nullable();
            $table->text('response')->nullable();
            $table->json('pg_response_json')->nullable();
            $table->dateTime('payment_date')->nullable();
            $table->string('currency', 10)->nullable();
            $table->enum('status', ['successful', 'failed', 'pending']);
            $table->text('rejection_reason')->nullable();
            $table->string('receipt_image')->nullable();
            $table->text('order_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('verification_status', 150)->default('Pending');
            $table->string('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->text('remarks')->nullable();
            $table->double('tds_amount')->default(0);
            $table->text('tdsReason')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
