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
        Schema::create('visa_clearance_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id')->nullable();
            $table->string('event_year', 10)->nullable();
            
            // Delegate Details
            $table->string('organisation_name', 255);
            $table->string('designation', 255);
            $table->string('passport_name', 255);
            $table->string('father_husband_name', 255);
            $table->date('dob');
            $table->string('place_of_birth', 255);
            $table->string('nationality', 100);
            
            // Passport Details
            $table->string('passport_number', 100);
            $table->date('passport_issue_date');
            $table->string('passport_issue_place', 255);
            $table->date('passport_expiry_date');
            $table->date('entry_date_india');
            $table->date('exit_date_india');
            
            // Contact Details
            $table->string('phone_country_code', 10)->nullable();
            $table->string('phone_number', 20);
            $table->string('email', 255);
            
            // Address in Country of Residence
            $table->string('address_line1', 255);
            $table->string('address_line2', 255)->nullable();
            $table->string('city', 100);
            $table->string('state', 100);
            $table->string('country', 100);
            $table->string('postal_code', 20);
            
            // Metadata
            $table->string('source_url', 500)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            
            // Status & Workflow
            $table->string('status', 50)->default('pending');
            $table->text('status_comment')->nullable();
            
            // Assignment
            $table->unsignedBigInteger('assigned_to_user_id')->nullable();
            $table->string('assigned_to_name', 255)->nullable();
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('event_id');
            $table->index('event_year');
            $table->index('email');
            $table->index('passport_number');
            $table->index('status');
            $table->index('assigned_to_user_id');
            $table->index('created_at');
            
            // Foreign Keys
            $table->foreign('event_id')->references('id')->on('events')->onDelete('set null');
            $table->foreign('assigned_to_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visa_clearance_requests');
    }
};
