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
        Schema::create('billing_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->string('billing_company');
            $table->string('contact_name');
            $table->string('email');
            $table->string('phone');
            $table->string('address');
            $table->string('gst_id')->nullable();
            $table->string('city_id')->nullable();
            $table->foreignId('state_id')->constrained('states');
            $table->foreignId('country_id')->constrained('countries');
            $table->string('postal_code');
            $table->boolean('same_as_basic')->default(false);
            $table->timestamps();

            // Indexes
            $table->index('application_id');
            $table->index('country_id');
            $table->index('state_id');
            $table->index('city_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_details');
    }
};
