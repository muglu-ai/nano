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
        Schema::create('poster_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('tin_no')->unique(); // Transaction ID
            $table->string('token')->unique(); // Reference to demo table
            
            // Registration Details
            $table->string('sector');
            $table->enum('currency', ['INR', 'USD'])->default('INR');
            
            // Abstract/Poster Details
            $table->string('poster_category')->default('Breaking Boundaries');
            $table->string('abstract_title');
            $table->text('abstract');
            $table->string('extended_abstract_path')->nullable();
            $table->string('extended_abstract_original_name')->nullable();
            
            // Authors (stored in separate columns for easy querying)
            $table->json('authors'); // Complete author data
            $table->integer('lead_author_index')->nullable();
            $table->integer('presenter_index')->nullable();
            
            // Extracted lead author details for quick access
            $table->string('lead_author_name')->nullable();
            $table->string('lead_author_email')->nullable();
            $table->string('lead_author_mobile')->nullable();
            
            // Presentation Preference
            $table->string('presentation_mode')->default('Poster only');
            
            // Pricing Details
            $table->integer('attendee_count')->default(0);
            $table->decimal('base_amount', 10, 2)->default(0);
            $table->decimal('gst_amount', 10, 2)->default(0);
            $table->decimal('processing_fee', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            
            // Permissions
            $table->boolean('publication_permission')->default(false);
            $table->boolean('authors_approval')->default(false);
            
            // Payment Status
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->string('payment_method')->nullable(); // CCAvenue, PayPal
            $table->string('payment_transaction_id')->nullable();
            $table->timestamp('payment_date')->nullable();
            
            $table->string('status')->default('submitted'); // submitted, confirmed, rejected
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poster_registrations');
    }
};
