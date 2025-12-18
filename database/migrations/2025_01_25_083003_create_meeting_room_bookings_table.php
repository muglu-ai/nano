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
        if (!Schema::hasTable('meeting_room_bookings')) {
            Schema::create('meeting_room_bookings', function (Blueprint $table) {
                $table->integerIncrements('id'); // int UNSIGNED AUTO_INCREMENT
                $table->string('booking_id')->default('0');
                $table->unsignedBigInteger('application_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                // Make these UNSIGNED to be compatible with referenced IDs
                $table->unsignedInteger('room_type_id')->nullable();
                $table->unsignedInteger('slot_id')->nullable();
                $table->date('booking_date')->nullable();
                $table->boolean('is_member')->nullable();
                $table->decimal('final_price', 10, 2)->nullable();
                $table->enum('payment_status', ['pending', 'paid', 'cancelled'])->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
                $table->enum('confirmation_status', ['confirmed', 'pending', 'canceled', 'rejected'])->nullable();

                // Indexes
                $table->index('application_id');
                $table->index('user_id');
                $table->index('room_type_id');
                $table->index('slot_id');
                $table->index('booking_id');

                // Foreign key constraints
                $table->foreign('application_id')->references('id')->on('applications')->onDelete('set null');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('room_type_id')->references('id')->on('meeting_room_types')->onDelete('set null');
                $table->foreign('slot_id')->references('id')->on('meeting_room_slots')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_room_bookings');
    }
};

