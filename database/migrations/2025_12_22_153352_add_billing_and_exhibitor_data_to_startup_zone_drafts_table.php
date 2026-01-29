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
        if (Schema::hasTable('startup_zone_drafts')) {
            Schema::table('startup_zone_drafts', function (Blueprint $table) {
                if (!Schema::hasColumn('startup_zone_drafts', 'billing_data')) {
                    $table->json('billing_data')->nullable()->after('contact_data');
                }
                if (!Schema::hasColumn('startup_zone_drafts', 'exhibitor_data')) {
                    $table->json('exhibitor_data')->nullable()->after('billing_data');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('startup_zone_drafts')) {
            Schema::table('startup_zone_drafts', function (Blueprint $table) {
                if (Schema::hasColumn('startup_zone_drafts', 'billing_data')) {
                    $table->dropColumn('billing_data');
                }
                if (Schema::hasColumn('startup_zone_drafts', 'exhibitor_data')) {
                    $table->dropColumn('exhibitor_data');
                }
            });
        }
    }
};
