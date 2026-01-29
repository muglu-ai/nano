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
            if (!Schema::hasColumn('posters', 'tin_no')) {
                $table->string('tin_no', 50)->unique()->nullable()->after('id');
            }
            if (!Schema::hasColumn('posters', 'pin_no')) {
                $table->string('pin_no', 50)->nullable()->after('tin_no');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posters', function (Blueprint $table) {
            $table->dropColumn(['tin_no', 'pin_no']);
        });
    }
};
