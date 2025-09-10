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
        // add gst_no, pan_no, and tan_no, participant_type, gst_compliance, certificate
        Schema::table('applications', function (Blueprint $table) {
            $table->string('gst_no')->nullable();
            $table->string('pan_no')->nullable();
            $table->string('tan_no')->nullable();
            $table->string('participation_type')->nullable();
            $table->boolean('gst_compliance')->default(false);
            $table->string('certificate')->nullable();
            //add is pavilion flag
            $table->boolean('is_pavilion')->default(false);
            //add allocated_sqm
            $table->integer('allocated_sqm')->nullable();
            // add pavilion_id nullable no foreign key
            $table->integer('pavilion_id')->nullable();




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
