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
        Schema::table('ticket_types', function (Blueprint $table) {
            // Add national and international pricing columns (skip if already exist)
            if (!Schema::hasColumn('ticket_types', 'early_bird_price_national')) {
                $table->decimal('early_bird_price_national', 10, 2)->nullable()->after('early_bird_price');
            }
            if (!Schema::hasColumn('ticket_types', 'early_bird_price_international')) {
                $table->decimal('early_bird_price_international', 10, 2)->nullable()->after('early_bird_price_national');
            }
            if (!Schema::hasColumn('ticket_types', 'regular_price_national')) {
                $table->decimal('regular_price_national', 10, 2)->nullable()->after('regular_price');
            }
            if (!Schema::hasColumn('ticket_types', 'regular_price_international')) {
                $table->decimal('regular_price_international', 10, 2)->nullable()->after('regular_price_national');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_types', function (Blueprint $table) {
            $table->dropColumn([
                'early_bird_price_national',
                'early_bird_price_international',
                'regular_price_national',
                'regular_price_international',
            ]);
        });
    }
};
