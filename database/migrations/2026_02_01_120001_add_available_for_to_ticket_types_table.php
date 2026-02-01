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
        Schema::table('ticket_types', function (Blueprint $table) {
            if (!Schema::hasColumn('ticket_types', 'available_for')) {
                $table->enum('available_for', ['national', 'international', 'both'])
                      ->default('both')
                      ->after('sort_order')
                      ->comment('Controls ticket visibility: national (Indian only), international (non-Indian only), or both');
            }
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
