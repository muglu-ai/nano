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
        Schema::create('poster_registration_demos', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();
            $table->string('session_id')->nullable();
            
            // Registration Details
            $table->string('sector');
            $table->enum('currency', ['INR', 'USD'])->default('INR');
            
            // Abstract/Poster Details
            $table->string('poster_category')->default('Breaking Boundaries');
            $table->string('abstract_title');
            $table->text('abstract');
            $table->string('extended_abstract_path')->nullable();
            $table->string('extended_abstract_original_name')->nullable();
            
            // Authors (JSON storage for flexible author count)
            $table->json('authors'); // Will store array of authors with all their details
            $table->integer('lead_author_index')->nullable(); // Which author is the lead (1-4)
            $table->integer('presenter_index')->nullable(); // Which author is the presenter (1-4)
            
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
            
            $table->string('status')->default('draft'); // draft, preview, submitted
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poster_registration_demos');
    }
};
