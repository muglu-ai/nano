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
        Schema::create('event_participations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->enum('participation_type', ['onsite', 'hybrid', 'online']);
            $table->enum('region', ['Indian', 'International']);
            $table->boolean('previous_participation');
            $table->json('stall_categories'); // Array for multiple categories
            $table->integer('interested_sqm');
            $table->json('product_groups'); // Array for product groups
            $table->json('sectors'); // Array for sectors
            $table->boolean('terms_accepted');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_participations');
    }
};
