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
        Schema::create('elevateregistration', function (Blueprint $table) {
            $table->id();
            
            // Company Information
            $table->string('company_name');
            $table->text('address');
            $table->string('country');
            $table->string('state');
            $table->string('city');
            $table->string('postal_code');
            
            // Attendance Information
            $table->enum('attendance', ['yes', 'no']);
            $table->text('attendance_reason')->nullable();
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('company_name');
            $table->index('attendance');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elevateregistration');
    }
};
