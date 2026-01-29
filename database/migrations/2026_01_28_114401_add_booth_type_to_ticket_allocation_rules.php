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
            if (!Schema::hasColumn('ticket_allocation_rules', 'booth_type')) {
                $table->string('booth_type')->nullable()->after('application_type');
                $table->index('booth_type', 'idx_tar_booth_type');
            }
            // Make booth_area_min and booth_area_max nullable (required only for numeric ranges)
            if (Schema::hasColumn('ticket_allocation_rules', 'booth_area_min')) {
                $table->integer('booth_area_min')->nullable()->change();
            }
            if (Schema::hasColumn('ticket_allocation_rules', 'booth_area_max')) {
                $table->integer('booth_area_max')->nullable()->change();
            }
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
