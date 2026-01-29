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
        Schema::table('billing_details', function (Blueprint $table) {
            if (!Schema::hasColumn('billing_details', 'has_indian_gst')) {
                $table->string('has_indian_gst', 10)->nullable()->after('gst_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billing_details', function (Blueprint $table) {
            $table->dropColumn('has_indian_gst');
        });
    }
};
