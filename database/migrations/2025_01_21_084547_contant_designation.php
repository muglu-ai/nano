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
        // Add new field designation to event_contacts table only if it doesn't exist
        if (Schema::hasTable('event_contacts')) {
            Schema::table('event_contacts', function (Blueprint $table) {
                if (!Schema::hasColumn('event_contacts', 'designation')) {
                    $table->string('designation')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('event_contacts')) {
            Schema::table('event_contacts', function (Blueprint $table) {
                if (Schema::hasColumn('event_contacts', 'designation')) {
                    $table->dropColumn('designation');
                }
            });
        }
    }
};
