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
        if (!Schema::hasTable('meeting_room_types')) {
            Schema::create('meeting_room_types', function (Blueprint $table) {
            $table->integerIncrements('id'); // int NOT NULL AUTO_INCREMENT in SQL
            $table->string('room_type', 50)->nullable();
            $table->integer('capacity')->nullable();
            $table->integer('size_sqm')->nullable();
            $table->integer('qty')->nullable();
            $table->string('location', 255)->nullable();
            $table->text('equipment')->nullable();
            $table->text('fnb')->nullable();
            $table->decimal('member_price', 10, 2)->nullable();
            $table->decimal('non_member_price', 10, 2)->nullable();
            $table->string('currency', 10)->nullable();
                $table->text('notes')->nullable();
                // Note: No timestamps in SQL schema
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_room_types');
    }
};

