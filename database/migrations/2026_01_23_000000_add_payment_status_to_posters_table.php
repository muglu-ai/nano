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
        Schema::table('posters', function (Blueprint $table) {
            $table->enum('payment_status', ['pending', 'successful', 'failed'])->default('pending')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posters', function (Blueprint $table) {
            $table->dropColumn('payment_status');
        });
    }
};
