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
            if (!Schema::hasColumn('startup_zone_drafts', 'pricing_data')) {
                $table->json('pricing_data')->nullable()->after('exhibitor_data');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('startup_zone_drafts', function (Blueprint $table) {
            if (Schema::hasColumn('startup_zone_drafts', 'pricing_data')) {
                $table->dropColumn('pricing_data');
            }
        });
    }
};
