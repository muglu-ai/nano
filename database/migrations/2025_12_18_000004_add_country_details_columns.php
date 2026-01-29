<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('countries')) {
            Schema::table('countries', function (Blueprint $table) {
                // Add phonecode column right after code
                if (!Schema::hasColumn('countries', 'phonecode')) {
                    $table->string('phonecode', 10)->nullable()->after('code');
                }
                
                // Add flag emoji column
                if (!Schema::hasColumn('countries', 'flag')) {
                    $table->string('flag', 10)->nullable()->after('phonecode');
                }
                
                // Add ISO3 code
                if (!Schema::hasColumn('countries', 'iso3')) {
                    $table->string('iso3', 3)->nullable()->after('flag');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('countries')) {
            Schema::table('countries', function (Blueprint $table) {
                $columns = ['phonecode', 'flag', 'iso3'];
                
                foreach ($columns as $column) {
                    if (Schema::hasColumn('countries', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
