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
        Schema::table('ticket_allocation_rules', function (Blueprint $table) {
            // Add booth_type for special booth types (POD, Booth / POD, Startup Booth, etc.)
            $table->string('booth_type')->nullable()->after('application_type');
            
            // Make booth_area_min and booth_area_max nullable (required only for numeric ranges)
            $table->integer('booth_area_min')->nullable()->change();
            $table->integer('booth_area_max')->nullable()->change();
            
            // Add index for booth_type lookups
            $table->index('booth_type', 'idx_tar_booth_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_allocation_rules', function (Blueprint $table) {
            $table->dropIndex('idx_tar_booth_type');
            $table->dropColumn('booth_type');
            
            // Revert booth_area fields to not null (if needed)
            // Note: This might fail if there are null values, so handle carefully
            $table->integer('booth_area_min')->nullable(false)->change();
            $table->integer('booth_area_max')->nullable(false)->change();
        });
    }
};
