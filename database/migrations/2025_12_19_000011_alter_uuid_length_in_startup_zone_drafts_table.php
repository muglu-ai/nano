<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('startup_zone_drafts')) {
            // Ensure the uuid column can store full 36-character UUIDs
            DB::statement("ALTER TABLE `startup_zone_drafts` MODIFY `uuid` CHAR(36) NULL");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('startup_zone_drafts')) {
            // Revert to previous length (35) if needed
            DB::statement("ALTER TABLE `startup_zone_drafts` MODIFY `uuid` CHAR(35) NULL");
        }
    }
};
