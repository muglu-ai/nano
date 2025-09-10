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
        //
        Schema::table('co_exhibitors', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // Admin approval
            $table->unsignedBigInteger('user_id')->nullable(); // Link to user account
            $table->integer('allocated_passes')->default(0); // Pass allocation
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
