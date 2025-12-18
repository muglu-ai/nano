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
        // Modify events table with start and end date columns if missing
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                if (!Schema::hasColumn('events', 'start_date')) {
                    $table->date('start_date')->nullable();
                }
                if (!Schema::hasColumn('events', 'end_date')) {
                    $table->date('end_date')->nullable();
                }
            });
        }

        // Seed values in events table (safe even if events already exist)
        DB::table('events')->insert([
            'event_year' => date('Y'),
            'event_name' => 'Tech Summit',
            'start_date' => now(),
            'end_date' => now()->addDays(3),
            'event_date' => now(),
            'event_location' => 'Bengaluru',
            'event_description' => 'Tech Summit is a conference that brings together the best and brightest minds in the tech industry. Join us for a day of learning, networking, and fun!',
            'event_image' => 'tech_summit.jpg',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Add foreign key constraint to existing event_id column on applications (ignore if already exists)
        if (Schema::hasTable('applications') && Schema::hasTable('events')) {
            Schema::table('applications', function (Blueprint $table) {
                // We cannot easily introspect FK names here; rely on DB structure import to avoid duplicates
                if (Schema::hasColumn('applications', 'event_id')) {
                    $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: we won't drop seeded data or constraints here to avoid conflicts with imported schema
    }
};
