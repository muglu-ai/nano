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
        Schema::table('ticket_registrations', function (Blueprint $table) {
            if (!Schema::hasColumn('ticket_registrations', 'registration_type')) {
                $table->string('registration_type')->nullable()->after('contact_id');
            }
            // Make company_name nullable for Individual registrations
            if (Schema::hasColumn('ticket_registrations', 'company_name')) {
                $table->string('company_name')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_registrations', function (Blueprint $table) {
            $table->dropColumn('registration_type');
            // Revert company_name to not nullable (if needed)
            $table->string('company_name')->nullable(false)->change();
        });
    }
};
