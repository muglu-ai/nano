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
        Schema::create('stall_manning', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exhibition_participant_id')->constrained('exhibition_participants')->onDelete('cascade');
            $table->string('unique_id', 25)->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable()->comment('varchar(255)');
            $table->string('last_name')->nullable();
            $table->string('email');
            $table->string('mobile', 25)->nullable();
            $table->string('job_title')->nullable();
            $table->string('organisation_name')->nullable();
            $table->string('ticketType', 250)->nullable();
            $table->string('token')->nullable();
            $table->string('id_type', 125)->nullable();
            $table->string('id_no', 125)->nullable();
            $table->string('confirmedCategory', 125)->nullable();
            $table->string('pinNo', 50)->nullable();
            $table->json('api_data')->nullable();
            $table->json('api_response')->nullable();
            $table->boolean('api_sent')->nullable();
            $table->boolean('emailSent')->default(false);
            $table->json('reminder')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('exhibition_participant_id');
            $table->index('unique_id');
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
