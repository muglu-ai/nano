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
        if (Schema::hasTable('ticket_upgrades')) {
            Schema::table('ticket_upgrades', function (Blueprint $table) {
                if (!Schema::hasColumn('ticket_upgrades', 'upgrade_request_id')) {
                    $table->foreignId('upgrade_request_id')->nullable()->after('id')->constrained('ticket_upgrade_requests')->onDelete('set null');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ticket_upgrades')) {
            Schema::table('ticket_upgrades', function (Blueprint $table) {
                if (Schema::hasColumn('ticket_upgrades', 'upgrade_request_id')) {
                    $table->dropForeign(['upgrade_request_id']);
                    $table->dropColumn('upgrade_request_id');
                }
            });
        }
    }
};
