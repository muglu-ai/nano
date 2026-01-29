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
        if (!Schema::hasTable('meeting_rooms')) {
            Schema::create('meeting_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique(); // Unique constraint as per SQL
            $table->string('name');
            $table->integer('capacity');
            $table->integer('size_sqm');
            $table->string('location');
            $table->unsignedInteger('quantity_available');
            $table->decimal('price_member', 10, 2);
                $table->decimal('price_non_member', 10, 2);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_rooms');
    }
};

