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
            // Add USD rates for booth pricing
            if (!Schema::hasColumn('event_configurations', 'shell_scheme_rate_usd')) {
                $table->decimal('shell_scheme_rate_usd', 10, 2)->nullable()->after('shell_scheme_rate');
            }
            if (!Schema::hasColumn('event_configurations', 'raw_space_rate_usd')) {
                $table->decimal('raw_space_rate_usd', 10, 2)->nullable()->after('raw_space_rate');
            }
        });

        // Set default values for existing records
        // Default values based on constants.php: 175 for shell scheme, 160 for raw space
        DB::table('event_configurations')->whereNull('shell_scheme_rate_usd')->update([
            'shell_scheme_rate_usd' => 175.00,
            'raw_space_rate_usd' => 160.00,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_configurations', function (Blueprint $table) {
            $table->dropColumn(['shell_scheme_rate_usd', 'raw_space_rate_usd']);
        });
    }
};
