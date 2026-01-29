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
        if (Schema::hasTable('ticket_orders')) {
            Schema::table('ticket_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('ticket_orders', 'payment_status')) {
                    // Add payment_status enum field
                    $table->enum('payment_status', ['pending', 'paid', 'complimentary', 'cancelled', 'refunded'])
                        ->default('pending')
                        ->after('status');

                    // Add index for better query performance
                    $table->index('payment_status');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ticket_orders')) {
            Schema::table('ticket_orders', function (Blueprint $table) {
                $table->dropIndex(['payment_status']);
                $table->dropColumn('payment_status');
            });
        }
    }
};
