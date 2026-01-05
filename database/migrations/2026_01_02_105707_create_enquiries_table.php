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
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id')->nullable();
            $table->string('event_year', 10)->nullable();
            
            // Personal Information
            $table->string('title', 10)->nullable();
            $table->string('first_name', 255)->nullable();
            $table->string('last_name', 255)->nullable();
            $table->string('full_name', 255);
            
            // Organization Information
            $table->string('organisation', 255);
            $table->string('designation', 255);
            $table->string('sector', 200)->nullable();
            
            // Contact Information
            $table->string('email', 255);
            $table->string('phone_country_code', 5)->nullable();
            $table->string('phone_number', 20);
            $table->string('phone_full', 30)->nullable();
            
            // Address Information
            $table->string('city', 100);
            $table->string('state', 100)->nullable();
            $table->string('country', 100);
            $table->string('postal_code', 10)->nullable();
            $table->text('address')->nullable();
            
            // Enquiry Details
            $table->text('comments');
            $table->string('referral_source', 100)->nullable();
            
            // Metadata
            $table->string('source_url', 500)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            
            // Status & Workflow
            $table->string('status', 50)->default('new');
            $table->string('prospect_level', 50)->nullable();
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
        Schema::dropIfExists('enquiries');
    }
};
