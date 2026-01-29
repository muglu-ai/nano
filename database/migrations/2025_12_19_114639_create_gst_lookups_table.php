<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gst_lookups', function (Blueprint $table) {
            $table->id();
            $table->string('gst_number', 15)->unique();
            $table->string('company_name')->nullable();
            $table->text('billing_address')->nullable();
            $table->string('state_code', 2)->nullable();
            $table->string('state_name')->nullable();
            $table->string('pincode', 10)->nullable();
            $table->string('trade_name')->nullable();
            $table->string('registration_type')->nullable();
            $table->date('registration_date')->nullable();
            $table->string('status')->nullable(); // Active, Cancelled, etc.
            $table->json('raw_response')->nullable(); // Store full API response
            $table->integer('api_calls')->default(0); // Track how many times API was called
            $table->timestamp('last_verified_at')->nullable();
            $table->timestamps();
            
            $table->index('gst_number');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gst_lookups');
    }
};
