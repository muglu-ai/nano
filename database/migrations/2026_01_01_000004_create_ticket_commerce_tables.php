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
        // Ticket Orders - Checkout orders
        if (!Schema::hasTable('ticket_orders')) {
            Schema::create('ticket_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('ticket_registrations')->onDelete('cascade');
            $table->string('order_no')->unique();
            $table->decimal('subtotal', 10, 2)->default(0); // Sum of all item subtotals
            $table->decimal('gst_total', 10, 2)->default(0); // Total GST across all items
            $table->decimal('processing_charge_total', 10, 2)->default(0); // Total processing charges
            $table->decimal('discount_amount', 10, 2)->default(0); // Promo code discount
            $table->unsignedBigInteger('promo_code_id')->nullable(); // Foreign key added in later migration
            $table->decimal('total', 10, 2)->default(0); // Final total
            $table->enum('status', ['pending', 'paid', 'cancelled', 'refunded'])->default('pending');
            $table->timestamps();

            $table->index(['registration_id', 'status']);
            $table->index('order_no');
            });
        }

        // Ticket Order Items - Order line items
        if (!Schema::hasTable('ticket_order_items')) {
            Schema::create('ticket_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('ticket_orders')->onDelete('cascade');
            $table->foreignId('ticket_type_id')->constrained('ticket_types')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 10, 2); // Quantity × unit_price
            $table->decimal('gst_rate', 5, 2)->default(0); // GST rate percentage (e.g., 18)
            $table->decimal('gst_amount', 10, 2)->default(0); // GST amount calculated
            $table->decimal('processing_charge_rate', 5, 2)->default(0); // Processing charge percentage (e.g., 3 or 9)
            $table->decimal('processing_charge_amount', 10, 2)->default(0); // Processing charge amount
            $table->decimal('total', 10, 2); // subtotal + gst_amount + processing_charge_amount
            $table->string('pricing_type')->nullable(); // 'early_bird' or 'regular' - snapshot
            $table->timestamps();

            $table->index('order_id');
            });
        }

        // Ticket Payments - Payment records
        if (!Schema::hasTable('ticket_payments')) {
            Schema::create('ticket_payments', function (Blueprint $table) {
            $table->id();
            $table->json('order_ids_json'); // Array of order_ids - supports multiple orders
            $table->string('method')->default('card'); // Changed from enum to string to support any payment method
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'refunded'])->default('pending');
            $table->string('gateway_txn_id')->nullable();
            $table->string('gateway_name')->nullable(); // 'ccavenue', 'paypal', etc.
            $table->timestamp('paid_at')->nullable();
            // Payment Gateway logging
            $table->json('pg_request_json')->nullable(); // Full request sent to payment gateway
            $table->json('pg_response_json')->nullable(); // Full response received from payment gateway
            $table->json('pg_webhook_json')->nullable(); // Webhook payload received
            $table->timestamps();

            $table->index('gateway_txn_id');
            $table->index('status');
            });
        }

        // Ticket Payment Events - Webhook audit logs
        if (!Schema::hasTable('ticket_payment_events')) {
            Schema::create('ticket_payment_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('ticket_payments')->onDelete('cascade');
            $table->string('event_type'); // 'webhook_received', 'webhook_processed', 'manual_update'
            $table->json('payload_json')->nullable();
            $table->timestamps();

            $table->index(['payment_id', 'event_type']);
            });
        }

        // Ticket Receipts - Receipt generation
        if (!Schema::hasTable('ticket_receipts')) {
            Schema::create('ticket_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('ticket_registrations')->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained('ticket_orders')->onDelete('set null');
            $table->enum('type', ['provisional', 'acknowledgment'])->default('acknowledgment');
            $table->string('receipt_no')->unique();
            $table->string('file_path')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();

            $table->index(['registration_id', 'type']);
            $table->index('receipt_no');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_receipts');
        Schema::dropIfExists('ticket_payment_events');
        Schema::dropIfExists('ticket_payments');
        Schema::dropIfExists('ticket_order_items');
        Schema::dropIfExists('ticket_orders');
    }
};

