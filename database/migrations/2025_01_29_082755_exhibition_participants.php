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
        Schema::create('exhibition_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->nullable()->constrained('applications')->onDelete('cascade');
            $table->foreignId('coExhibitor_id')->nullable()->constrained('exhibition_participants');
            $table->integer('stall_manning_count')->default(0);
            $table->integer('complimentary_delegate_count')->default(0);
            $table->json('ticketAllocation')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('application_id');
            $table->index('coExhibitor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
