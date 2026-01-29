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
        // Add per-day pricing fields to ticket_types table
        Schema::table('ticket_types', function (Blueprint $table) {
            if (!Schema::hasColumn('ticket_types', 'per_day_price_national')) {
                $table->decimal('per_day_price_national', 10, 2)->nullable()->after('regular_price_international')
                    ->comment('Per-day price for national users (INR)');
            }
            if (!Schema::hasColumn('ticket_types', 'per_day_price_international')) {
                $table->decimal('per_day_price_international', 10, 2)->nullable()->after('per_day_price_national')
                    ->comment('Per-day price for international users (USD)');
            }
        });

        // Add selected_event_day_id to ticket_order_items table
        Schema::table('ticket_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('ticket_order_items', 'selected_event_day_id')) {
                $table->unsignedBigInteger('selected_event_day_id')->nullable()->after('ticket_type_id')
                    ->comment('The specific day user selected for this ticket');

                $table->foreign('selected_event_day_id')
                    ->references('id')
                    ->on('event_days')
                    ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_order_items', function (Blueprint $table) {
            $table->dropForeign(['selected_event_day_id']);
            $table->dropColumn('selected_event_day_id');
        });

        Schema::table('ticket_types', function (Blueprint $table) {
            $table->dropColumn(['per_day_price_national', 'per_day_price_international']);
        });
    }
};
