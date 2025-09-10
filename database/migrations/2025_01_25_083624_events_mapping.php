<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //modify event table with start date and end date column
        Schema::table('events', function (Blueprint $table) {
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
        });
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

        Schema::table('applications', function (Blueprint $table) {
            $table->foreignId('event_id')->default(1)->constrained();
        });



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
