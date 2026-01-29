<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change method column from enum to string
        Schema::table('ticket_payments', function (Blueprint $table) {
            // First, we need to drop the enum and recreate as string
            // MySQL doesn't support direct enum to string conversion, so we use raw SQL
            DB::statement("ALTER TABLE `ticket_payments` MODIFY COLUMN `method` VARCHAR(255) DEFAULT 'card'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to enum (if needed)
        Schema::table('ticket_payments', function (Blueprint $table) {
            DB::statement("ALTER TABLE `ticket_payments` MODIFY COLUMN `method` ENUM('upi', 'netbanking', 'card', 'manual', 'offline') DEFAULT 'card'");
        });
    }
};
