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
            $table->enum('salutation', ['Dr.', 'Mr.', 'Mrs.', 'Ms.']);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('job_title');
            $table->string('email');
            $table->string('contact_number');
            $table->string('secondary_email')->nullable();
            $table->timestamps();
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
