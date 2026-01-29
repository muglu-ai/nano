<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * TO-DO: Delete this as we have already created the events table in the previous migration file
     */

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //

        //seed values in event table
        DB::table('events')->insert([
            'event_year' => date('Y') ,
            'event_name' => 'Tech Summit',
            'start_date'=> now(),
            'end_date'=> now()->addDays(3),
            'event_date' => now(),
            'event_location' => 'Bengaluru',
            'event_description' => 'Tech Summit is a conference that brings together the best and brightest minds in the tech industry. Join us for a day of learning, networking, and fun!',
            'event_image' => 'tech_summit.jpg',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
