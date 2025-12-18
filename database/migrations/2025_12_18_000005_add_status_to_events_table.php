<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                if (!Schema::hasColumn('events', 'status')) {
                    // Add status column as enum
                    DB::statement("ALTER TABLE events ADD COLUMN status ENUM('upcoming', 'ongoing', 'over') DEFAULT 'upcoming' AFTER end_date");
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                if (Schema::hasColumn('events', 'status')) {
                    $table->dropColumn('status');
                }
            });
        }
    }
};
