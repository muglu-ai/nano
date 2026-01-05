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
        Schema::create('enquiry_followups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enquiry_id');
            
            // Followup Details
            $table->string('followup_type', 50)->nullable();
            $table->string('followup_status', 50)->nullable();
            $table->text('followup_comment')->nullable();
            $table->date('followup_date')->nullable();
            $table->time('followup_time')->nullable();
            $table->dateTime('followup_datetime')->nullable();
            
            // Assignment
            $table->unsignedBigInteger('assigned_to_user_id')->nullable();
            $table->string('assigned_to_name', 255)->nullable();
            
            // Prospect Tracking
            $table->string('prospect_level', 50)->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index('enquiry_id');
            $table->index('followup_date');
            $table->index('followup_status');
            $table->index('assigned_to_user_id');
            
            // Foreign Keys
            $table->foreign('enquiry_id')->references('id')->on('enquiries')->onDelete('cascade');
            $table->foreign('assigned_to_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enquiry_followups');
    }
};
