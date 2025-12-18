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
        if (!Schema::hasTable('meeting_room_slots')) {
            Schema::create('meeting_room_slots', function (Blueprint $table) {
                $table->integerIncrements('id'); // int UNSIGNED AUTO_INCREMENT
                // Make room_type_id UNSIGNED to be compatible with meeting_room_types.id
                $table->unsignedInteger('room_type_id')->nullable();
                $table->string('slot_name', 50)->nullable();
                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();
                // Note: No timestamps in SQL schema

                // Indexes
                $table->index('room_type_id');

                // Foreign key constraint
                $table->foreign('room_type_id')
                    ->references('id')
                    ->on('meeting_room_types')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_room_slots');
    }
};

