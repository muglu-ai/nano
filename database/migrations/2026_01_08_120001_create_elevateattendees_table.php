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
        Schema::create('elevateattendees', function (Blueprint $table) {
            $table->id();
            
            // Foreign Key to Registration
            $table->foreignId('registration_id')->constrained('elevateregistration')->onDelete('cascade');
            
            // Attendee Information
            $table->string('salutation', 10);
            $table->string('first_name', 255);
            $table->string('last_name', 255);
            $table->string('job_title', 255)->nullable(); // Optional field
            $table->string('email', 255);
            $table->string('phone_number', 20);
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('registration_id');
            $table->index('email');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elevateattendees');
    }
};
