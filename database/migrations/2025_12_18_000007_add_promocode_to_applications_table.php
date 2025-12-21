<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('applications')) {
            Schema::table('applications', function (Blueprint $table) {
                if (!Schema::hasColumn('applications', 'promocode')) {
                    $table->string('promocode', 100)->nullable()->after('RegSource');
                    $table->index('promocode');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('applications')) {
            Schema::table('applications', function (Blueprint $table) {
                if (Schema::hasColumn('applications', 'promocode')) {
                    $table->dropIndex(['promocode']);
                    $table->dropColumn('promocode');
                }
            });
        }
    }
};
