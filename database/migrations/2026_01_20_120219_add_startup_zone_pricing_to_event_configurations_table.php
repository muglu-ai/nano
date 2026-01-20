<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_configurations', function (Blueprint $table) {
            // Early bird cutoff date
            $table->date('startup_zone_early_bird_cutoff_date')->nullable()->after('gst_rate');
            
            // Regular prices (INR)
            $table->decimal('startup_zone_regular_price_inr', 10, 2)->nullable()->after('startup_zone_early_bird_cutoff_date');
            $table->decimal('startup_zone_regular_price_with_tv_inr', 10, 2)->nullable()->after('startup_zone_regular_price_inr');
            
            // Early bird prices (INR)
            $table->decimal('startup_zone_early_bird_price_inr', 10, 2)->nullable()->after('startup_zone_regular_price_with_tv_inr');
            $table->decimal('startup_zone_early_bird_price_with_tv_inr', 10, 2)->nullable()->after('startup_zone_early_bird_price_inr');
            
            // Regular prices (USD)
            $table->decimal('startup_zone_regular_price_usd', 10, 2)->nullable()->after('startup_zone_early_bird_price_with_tv_inr');
            $table->decimal('startup_zone_regular_price_with_tv_usd', 10, 2)->nullable()->after('startup_zone_regular_price_usd');
            
            // Early bird prices (USD)
            $table->decimal('startup_zone_early_bird_price_usd', 10, 2)->nullable()->after('startup_zone_regular_price_with_tv_usd');
            $table->decimal('startup_zone_early_bird_price_with_tv_usd', 10, 2)->nullable()->after('startup_zone_early_bird_price_usd');
        });

        // Set default values for existing records
        DB::table('event_configurations')->whereNull('startup_zone_early_bird_cutoff_date')->update([
            'startup_zone_early_bird_cutoff_date' => '2026-03-31',
            'startup_zone_regular_price_inr' => 52000.00,
            'startup_zone_regular_price_with_tv_inr' => 60000.00,
            'startup_zone_early_bird_price_inr' => 30000.00,
            'startup_zone_early_bird_price_with_tv_inr' => 37500.00,
            'startup_zone_regular_price_usd' => null, // Will be calculated based on exchange rate
            'startup_zone_regular_price_with_tv_usd' => null,
            'startup_zone_early_bird_price_usd' => null,
            'startup_zone_early_bird_price_with_tv_usd' => null,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_configurations', function (Blueprint $table) {
            $table->dropColumn([
                'startup_zone_early_bird_cutoff_date',
                'startup_zone_regular_price_inr',
                'startup_zone_regular_price_with_tv_inr',
                'startup_zone_early_bird_price_inr',
                'startup_zone_early_bird_price_with_tv_inr',
                'startup_zone_regular_price_usd',
                'startup_zone_regular_price_with_tv_usd',
                'startup_zone_early_bird_price_usd',
                'startup_zone_early_bird_price_with_tv_usd',
            ]);
        });
    }
};
