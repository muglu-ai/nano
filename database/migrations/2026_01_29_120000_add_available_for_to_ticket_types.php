<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * available_for: both = Indian & International; indian_only = Indian only; international_only = International only
     */
    public function up(): void
    {
        Schema::table('ticket_types', function (Blueprint $table) {
            $table->string('available_for', 32)->default('both')->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_types', function (Blueprint $table) {
            $table->dropColumn('available_for');
        });
    }
};
