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
        Schema::create('complimentary_delegates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exhibition_participant_id')->constrained('exhibition_participants')->onDelete('cascade');
            $table->string('ticketType', 125)->nullable();
            $table->string('title', 25)->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name', 250)->nullable();
            $table->string('last_name')->nullable();
            $table->string('email');
            $table->string('mobile', 25)->nullable();
            $table->string('job_title')->nullable();
            $table->string('organisation_name')->nullable();
            $table->string('token')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code', 25)->nullable();
            $table->text('buisness_nature')->nullable();
            $table->text('products')->nullable();
            $table->string('id_type', 150)->nullable();
            $table->string('id_no', 50)->nullable();
            $table->text('profile_pic')->nullable();
            $table->string('unique_id', 25)->nullable();
            $table->boolean('inaugural_session')->default(true);
            $table->boolean('inauguralConfirmation')->default(false);
            $table->text('approvedHistory')->nullable();
            $table->string('confirmedCategory', 100)->nullable();
            $table->boolean('lunchStatus')->default(false);
            $table->string('pinNo', 50)->nullable();
            $table->json('api_data')->nullable();
            $table->json('api_response')->nullable();
            $table->boolean('api_sent')->nullable();
            $table->boolean('emailSent')->default(false);
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
