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
        Schema::create('event_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->string('salutation', 25)->nullable();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('job_title')->nullable();
            $table->string('email');
            $table->string('contact_number')->nullable();
            $table->string('secondary_email')->nullable();
            $table->string('designation')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('application_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_contacts');
    }
};
