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
        // Add tin_no to poster_registration_demos table
        Schema::table('poster_registration_demos', function (Blueprint $table) {
            $table->string('tin_no')->unique()->nullable()->after('token');
        });
        
        // Add pin_no to poster_registrations table (set after payment)
        Schema::table('poster_registrations', function (Blueprint $table) {
            $table->string('pin_no')->unique()->nullable()->after('tin_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('poster_registration_demos', function (Blueprint $table) {
            $table->dropColumn('tin_no');
        });
        
        Schema::table('poster_registrations', function (Blueprint $table) {
            $table->dropColumn('pin_no');
        });
    }
};
