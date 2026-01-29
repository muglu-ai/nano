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
        if (Schema::hasTable('applications')) {
            Schema::table('applications', function (Blueprint $table) {
                // Add submission_status only if it doesn't already exist
                if (!Schema::hasColumn('applications', 'submission_status')) {
                    $table->string('submission_status')->default('in progress');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('applications')) {
            Schema::table('applications', function (Blueprint $table) {
                if (Schema::hasColumn('applications', 'submission_status')) {
                    $table->dropColumn('submission_status');
                }
            });
        }
    }
};
