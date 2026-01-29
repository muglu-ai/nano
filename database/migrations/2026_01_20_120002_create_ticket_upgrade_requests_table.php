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
        if (!Schema::hasTable('ticket_upgrade_requests')) {
            Schema::create('ticket_upgrade_requests', function (Blueprint $table) {
                $table->id();
                $table->enum('request_type', ['individual', 'group'])->default('individual');
                $table->foreignId('contact_id')->constrained('ticket_contacts')->onDelete('cascade');
                $table->foreignId('registration_id')->nullable()->constrained('ticket_registrations')->onDelete('cascade');
                $table->json('upgrade_data_json'); // JSON with old/new ticket details
                $table->decimal('price_difference', 10, 2)->default(0); // Remaining amount
                $table->decimal('gst_amount', 10, 2)->default(0);
                $table->decimal('processing_charge_amount', 10, 2)->default(0);
                $table->decimal('total_amount', 10, 2)->default(0);
                $table->foreignId('upgrade_order_id')->nullable()->constrained('ticket_orders')->onDelete('set null');
                $table->enum('status', ['pending', 'paid', 'failed', 'cancelled'])->default('pending');
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();

                $table->index(['contact_id', 'status']);
                $table->index(['registration_id', 'status']);
                $table->index('status');
                $table->index('expires_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_upgrade_requests');
    }
};
