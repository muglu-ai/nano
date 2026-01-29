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
        Schema::table('ticket_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('ticket_categories', 'is_exhibitor_only')) {
                $table->boolean('is_exhibitor_only')->default(false)->after('description');
                $table->index('is_exhibitor_only');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_categories', function (Blueprint $table) {
            $table->dropIndex(['is_exhibitor_only']);
            $table->dropColumn('is_exhibitor_only');
        });
    }
};
