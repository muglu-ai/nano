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
        Schema::create('ticket_allocation_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->nullable()->constrained('events')->onDelete('cascade');
            $table->string('application_type')->nullable(); // 'exhibitor-registration', 'startup-zone', or null for all
            $table->integer('booth_area_min'); // Minimum booth area (sqm)
            $table->integer('booth_area_max'); // Maximum booth area (sqm)
            $table->json('ticket_allocations'); // {"ticket_type_id": count} - CENTRALIZED: All allocations stored here
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['event_id', 'application_type', 'is_active']);
            $table->index(['booth_area_min', 'booth_area_max']);
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
