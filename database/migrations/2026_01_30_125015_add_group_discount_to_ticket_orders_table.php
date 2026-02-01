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
        Schema::table('ticket_orders', function (Blueprint $table) {
            // Group discount fields - applied when delegate count > 3
            $table->boolean('group_discount_applied')->default(false)->after('promo_code_id');
            $table->decimal('group_discount_rate', 5, 2)->default(0)->after('group_discount_applied'); // Percentage (e.g., 10 for 10%)
            $table->decimal('group_discount_amount', 10, 2)->default(0)->after('group_discount_rate'); // Calculated discount amount
            $table->integer('group_discount_min_delegates')->nullable()->after('group_discount_amount'); // Minimum delegates required (e.g., 4)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_orders', function (Blueprint $table) {
            $table->dropColumn([
                'group_discount_applied',
                'group_discount_rate',
                'group_discount_amount',
                'group_discount_min_delegates',
            ]);
        });
    }
};
