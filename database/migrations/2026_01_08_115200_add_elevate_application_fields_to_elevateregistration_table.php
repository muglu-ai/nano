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
        if (Schema::hasTable('elevateregistration')) {
            Schema::table('elevateregistration', function (Blueprint $table) {
                // Add elevate application call names (JSON to store array of selected options)
                if (!Schema::hasColumn('elevateregistration', 'elevate_application_call_names')) {
                    $table->json('elevate_application_call_names')->nullable()->after('postal_code');
                }
                
                // Add ELEVATE 2025 ID
                if (!Schema::hasColumn('elevateregistration', 'elevate_2025_id')) {
                    $table->string('elevate_2025_id', 50)->nullable()->after('elevate_application_call_names');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('elevateregistration')) {
            Schema::table('elevateregistration', function (Blueprint $table) {
                $columns = ['elevate_application_call_names', 'elevate_2025_id'];
                
                foreach ($columns as $column) {
                    if (Schema::hasColumn('elevateregistration', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
