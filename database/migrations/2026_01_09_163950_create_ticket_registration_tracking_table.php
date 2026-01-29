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
        Schema::create('ticket_registration_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('tracking_token')->unique(); // Unique token to track a registration session
            $table->string('session_id')->nullable(); // Session ID for tracking
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            
            // Registration data (JSON)
            $table->json('registration_data')->nullable(); // Full registration form data
            
            // Status tracking
            $table->enum('status', [
                'started',           // Registration form accessed
                'in_progress',       // Form data saved (auto-save or step completion)
                'preview_viewed',    // Preview page viewed
                'payment_initiated', // Payment page accessed
                'payment_completed', // Payment successful
                'payment_failed',    // Payment failed
                'abandoned'          // User left without completing
            ])->default('started');
            
            // Timestamps for each stage
            $table->timestamp('started_at')->nullable();
            $table->timestamp('in_progress_at')->nullable();
            $table->timestamp('preview_viewed_at')->nullable();
            $table->timestamp('payment_initiated_at')->nullable();
            $table->timestamp('payment_completed_at')->nullable();
            $table->timestamp('payment_failed_at')->nullable();
            $table->timestamp('abandoned_at')->nullable();
            
            // Conversion tracking
            $table->foreignId('registration_id')->nullable()->constrained('ticket_registrations')->onDelete('set null');
            $table->foreignId('order_id')->nullable()->constrained('ticket_orders')->onDelete('set null');
            $table->string('order_no')->nullable();
            
            // Analytics fields
            $table->string('ticket_type_id')->nullable();
            $table->string('ticket_type_slug')->nullable();
            $table->string('nationality')->nullable();
            $table->integer('delegate_count')->nullable();
            $table->string('company_country')->nullable();
            $table->decimal('calculated_total', 10, 2)->nullable();
            $table->decimal('final_total', 10, 2)->nullable();
            
            // Dropoff tracking
            $table->string('dropoff_stage')->nullable(); // Where user dropped off
            $table->text('dropoff_reason')->nullable(); // Reason if available
            
            $table->timestamps();
            
            // Indexes for analytics
            $table->index(['event_id', 'status']);
            $table->index(['event_id', 'created_at']);
            $table->index('tracking_token');
            $table->index('session_id');
            $table->index('status');
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_registration_tracking');
    }
};
