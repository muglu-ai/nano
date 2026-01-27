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
        Schema::create('poster_authors', function (Blueprint $table) {
            $table->id();
            $table->string('token'); // References poster_registration_demos or poster_registrations
            $table->integer('author_index'); // 0-based index from form
            
            // Personal Information
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 200);
            $table->string('mobile', 20);
            
            // CV Upload
            $table->string('cv_path')->nullable();
            $table->string('cv_original_name')->nullable();
            
            // Roles
            $table->boolean('is_lead_author')->default(false);
            $table->boolean('is_presenter')->default(false);
            $table->boolean('will_attend')->default(false);
            
            // Residential Address
            $table->foreignId('country_id')->constrained('countries');
            $table->foreignId('state_id')->constrained('states');
            $table->string('city', 100);
            $table->string('postal_code', 20);
            
            // Affiliation Details
            $table->string('institution', 250);
            $table->string('affiliation_city', 100);
            $table->foreignId('affiliation_country_id')->constrained('countries');
            
            $table->timestamps();
            
            // Indexes for faster queries
            $table->index('token');
            $table->index(['token', 'author_index']);
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poster_authors');
    }
};
