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
        // Update payment_currency enum to include USD
        DB::statement("ALTER TABLE applications MODIFY COLUMN payment_currency ENUM('EUR', 'INR', 'USD') DEFAULT 'INR'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert payment_currency enum to original values
        DB::statement("ALTER TABLE applications MODIFY COLUMN payment_currency ENUM('EUR', 'INR') DEFAULT 'INR'");
    }
};
