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
        // create events table with columns event year, event name, event date, event location, event description and event image
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('event_year');
            $table->string('event_name');
            $table->date('event_date');
            $table->string('event_location');
            $table->text('event_description');
            $table->string('event_image');
            $table->timestamps();
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
