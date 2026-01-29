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
        Schema::create('enquiry_interests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enquiry_id');
            $table->string('interest_type', 50);
            $table->string('interest_other_detail', 255)->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('enquiry_id');
            $table->index('interest_type');
            
            // Foreign Keys
            $table->foreign('enquiry_id')->references('id')->on('enquiries')->onDelete('cascade');
            
            // Unique constraint
            $table->unique(['enquiry_id', 'interest_type'], 'unique_enquiry_interest');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enquiry_interests');
    }
};
