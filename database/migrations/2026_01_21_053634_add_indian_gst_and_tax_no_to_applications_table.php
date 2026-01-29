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
        Schema::table('applications', function (Blueprint $table) {
            if (!Schema::hasColumn('applications', 'indian_gst')) {
                $table->string('indian_gst', 10)->nullable()->after('tan_no');
            }
            if (!Schema::hasColumn('applications', 'tax_no')) {
                $table->string('tax_no', 100)->nullable()->after('indian_gst');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['indian_gst', 'tax_no']);
        });
    }
};
