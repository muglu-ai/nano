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
                if (!Schema::hasColumn('startup_zone_drafts', 'converted_to_application_id')) {
                    $table->unsignedBigInteger('converted_to_application_id')->nullable()->after('user_id');
                    $table->timestamp('converted_at')->nullable()->after('converted_to_application_id');
                    $table->index('converted_to_application_id');
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
                if (Schema::hasColumn('startup_zone_drafts', 'converted_to_application_id')) {
                    $table->dropIndex(['converted_to_application_id']);
                    $table->dropColumn(['converted_to_application_id', 'converted_at']);
                }
            });
        }
    }
};
