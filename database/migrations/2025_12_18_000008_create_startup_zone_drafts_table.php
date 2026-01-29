<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('startup_zone_drafts')) {
            Schema::create('startup_zone_drafts', function (Blueprint $table) {
                $table->id();
                $table->string('session_id', 255); // Session token for tracking
                // Use standard UUID length of 36 characters (including hyphens)
                $table->char('uuid', 36)->nullable(); // Alternative identifier
                
                // Booth Information
                $table->string('stall_category', 255)->nullable();
                $table->string('interested_sqm', 125)->nullable();
                
                // Company Information
                $table->string('company_name', 255)->nullable();
                $table->string('certificate_path', 255)->nullable(); // File path for uploaded certificate
                $table->integer('how_old_startup')->nullable();
                $table->string('address', 500)->nullable();
                $table->string('city_id', 255)->nullable();
                $table->foreignId('state_id')->nullable()->constrained('states')->onDelete('set null');
                $table->string('postal_code', 10)->nullable();
                $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('set null');
                $table->string('landline', 20)->nullable();
                $table->string('website', 255)->nullable();
                $table->string('company_email', 255)->nullable();
                
                // Tax Information (encrypted)
                $table->boolean('gst_compliance')->nullable();
                $table->text('gst_no')->nullable(); // Encrypted
                $table->text('pan_no')->nullable(); // Encrypted
                
                // Sector Information
                $table->string('sector_id', 255)->nullable();
                $table->string('subSector', 255)->nullable();
                $table->string('type_of_business', 255)->nullable();
                
                // Association & Promocode
                $table->string('promocode', 100)->nullable();
                $table->string('assoc_mem', 125)->nullable();
                $table->string('RegSource', 250)->nullable();
                
                // Contact Person Details (stored as JSON)
                $table->json('contact_data')->nullable(); // Stores: title, first_name, last_name, designation, email, mobile, country_code
                
                // Payment Information
                $table->string('payment_mode', 50)->nullable();
                
                // Metadata
                $table->string('application_type', 125)->default('startup-zone');
                $table->unsignedBigInteger('event_id')->default(1);
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('last_updated_field', 100)->nullable(); // Track which field was last updated
                $table->integer('progress_percentage')->default(0); // Form completion percentage
                $table->boolean('is_abandoned')->default(false); // Marked as abandoned after X days
                $table->timestamp('abandoned_at')->nullable();
                $table->timestamp('expires_at')->nullable(); // Auto-cleanup after expiration
                
                $table->timestamps();
            });
            
            // Add indexes separately
            try {
                Schema::table('startup_zone_drafts', function (Blueprint $table) {
                    $table->index('session_id');
                    $table->index('uuid');
                    $table->index('user_id');
                    $table->index('is_abandoned');
                    $table->index('expires_at');
                });
            } catch (QueryException $e) {
                // Indexes might already exist, continue
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('startup_zone_drafts');
    }
};
