<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('startup_zone_drafts')) {
            try {
                // Drop the old foreign key constraint if it exists
                DB::statement('ALTER TABLE `startup_zone_drafts` DROP FOREIGN KEY IF EXISTS `startup_zone_drafts_state_id_foreign`');
            } catch (QueryException $e) {
                // Foreign key might not exist or have different name, continue
            }
            
            // Check if states table exists, if not, we'll make state_id nullable without foreign key
            if (Schema::hasTable('states')) {
                try {
                    Schema::table('startup_zone_drafts', function (Blueprint $table) {
                        // Re-add foreign key pointing to correct table
                        $table->foreign('state_id')
                            ->references('id')
                            ->on('states')
                            ->onDelete('set null');
                    });
                } catch (QueryException $e) {
                    // Foreign key might already exist with correct reference, continue
                    if (strpos($e->getMessage(), 'Duplicate foreign key') === false) {
                        throw $e;
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('startup_zone_drafts')) {
            try {
                DB::statement('ALTER TABLE `startup_zone_drafts` DROP FOREIGN KEY IF EXISTS `startup_zone_drafts_state_id_foreign`');
            } catch (QueryException $e) {
                // Ignore if doesn't exist
            }
        }
    }
};
