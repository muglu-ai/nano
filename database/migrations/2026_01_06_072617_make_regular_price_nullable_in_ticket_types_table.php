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
            if (Schema::hasColumn('ticket_types', 'regular_price')) {
                // Make regular_price nullable since we're using national/international pricing
                $table->decimal('regular_price', 10, 2)->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_types', function (Blueprint $table) {
            // Revert to non-nullable (but this might fail if there are null values)
            $table->decimal('regular_price', 10, 2)->nullable(false)->change();
        });
    }
};
