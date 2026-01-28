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
        // Drop table if it exists (from previous failed migration)
        Schema::dropIfExists('ticket_allocation_rules');
        
        Schema::create('ticket_allocation_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->nullable()->constrained('events')->onDelete('cascade');
            $table->string('application_type')->nullable(); // 'exhibitor-registration', 'startup-zone', or null for all
            $table->string('booth_type')->nullable(); // Special booth type (POD, Booth / POD, Startup Booth, etc.) - null for numeric ranges
            $table->integer('booth_area_min')->nullable(); // Minimum booth area (sqm) - null if using booth_type
            $table->integer('booth_area_max')->nullable(); // Maximum booth area (sqm) - null if using booth_type
            $table->json('ticket_allocations'); // {"ticket_type_id": count} - CENTRALIZED: All allocations stored here
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['event_id', 'application_type', 'is_active'], 'idx_tar_event_app_active');
            $table->index(['booth_area_min', 'booth_area_max'], 'idx_tar_booth_area');
            $table->index('booth_type', 'idx_tar_booth_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_allocation_rules');
    }
};
