<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                if (!Schema::hasColumn('events', 'slug')) {
                    $table->string('slug', 255)->nullable()->after('event_name');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('events')) {
            Schema::table('events', function (Blueprint $table) {
                if (Schema::hasColumn('events', 'slug')) {
                    $table->dropColumn('slug');
                }
            });
        }
    }
};
