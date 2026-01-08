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
        Schema::create('elevate_registration_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 255); // Session token for tracking
            
            // Form data stored as JSON
            $table->json('form_data'); // Stores all form fields
            
            // Metadata
            $table->integer('progress_percentage')->default(0);
            $table->boolean('is_abandoned')->default(false);
            $table->timestamp('abandoned_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // Auto-cleanup after expiration (e.g., 7 days)
            $table->timestamp('converted_at')->nullable(); // When converted to final registration
            $table->unsignedBigInteger('converted_to_registration_id')->nullable(); // Link to final registration
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index('session_id');
            $table->index('is_abandoned');
            $table->index('expires_at');
            $table->index('converted_to_registration_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elevate_registration_sessions');
    }
};
