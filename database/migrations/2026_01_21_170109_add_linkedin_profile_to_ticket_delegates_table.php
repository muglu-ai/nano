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
        Schema::table('ticket_delegates', function (Blueprint $table) {
            if (!Schema::hasColumn('ticket_delegates', 'linkedin_profile')) {
                $table->string('linkedin_profile')->nullable()->after('job_title');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_delegates', function (Blueprint $table) {
            $table->dropColumn('linkedin_profile');
        });
    }
};
