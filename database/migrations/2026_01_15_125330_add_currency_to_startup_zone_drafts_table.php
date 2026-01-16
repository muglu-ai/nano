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
        Schema::table('startup_zone_drafts', function (Blueprint $table) {
            if (!Schema::hasColumn('startup_zone_drafts', 'currency')) {
                // Try to add after pricing_data, if it doesn't exist, add after exhibitor_data
                if (Schema::hasColumn('startup_zone_drafts', 'pricing_data')) {
                    $table->string('currency', 3)->nullable()->after('pricing_data');
                } else {
                    $table->string('currency', 3)->nullable()->after('exhibitor_data');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('startup_zone_drafts', function (Blueprint $table) {
            if (Schema::hasColumn('startup_zone_drafts', 'currency')) {
                $table->dropColumn('currency');
            }
        });
    }
};
