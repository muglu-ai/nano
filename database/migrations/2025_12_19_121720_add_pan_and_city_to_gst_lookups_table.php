<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('gst_lookups')) {
            Schema::table('gst_lookups', function (Blueprint $table) {
                if (!Schema::hasColumn('gst_lookups', 'pan')) {
                    $table->string('pan', 10)->nullable()->after('pincode');
                }
                if (!Schema::hasColumn('gst_lookups', 'city')) {
                    $table->string('city')->nullable()->after('pan');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('gst_lookups')) {
            Schema::table('gst_lookups', function (Blueprint $table) {
                if (Schema::hasColumn('gst_lookups', 'pan')) {
                    $table->dropColumn('pan');
                }
                if (Schema::hasColumn('gst_lookups', 'city')) {
                    $table->dropColumn('city');
                }
            });
        }
    }
};
