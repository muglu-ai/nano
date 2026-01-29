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
        if (Schema::hasTable('ticket_types') && !Schema::hasColumn('ticket_types', 'all_days_access')) {
            Schema::table('ticket_types', function (Blueprint $table) {
                $table->boolean('all_days_access')->default(false)->after('is_active');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ticket_types') && Schema::hasColumn('ticket_types', 'all_days_access')) {
            Schema::table('ticket_types', function (Blueprint $table) {
                $table->dropColumn('all_days_access');
            });
        }
    }
};

