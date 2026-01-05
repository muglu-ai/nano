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
        Schema::create('enquiry_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enquiry_id');
            $table->text('note');
            $table->string('note_type', 50)->default('general');
            
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->string('created_by_name', 255)->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('enquiry_id');
            $table->index('created_at');
            
            // Foreign Keys
            $table->foreign('enquiry_id')->references('id')->on('enquiries')->onDelete('cascade');
            $table->foreign('created_by_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enquiry_notes');
    }
};
