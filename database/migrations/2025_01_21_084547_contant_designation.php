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
        //add new field as designation to event_contacts table
        Schema::table('event_contacts', function (Blueprint $table) {
            $table->string('designation')->nullable();
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
