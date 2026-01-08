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
        if (Schema::hasTable('elevateregistration')) {
            Schema::table('elevateregistration', function (Blueprint $table) {
                // Add sector field after company_name
                if (!Schema::hasColumn('elevateregistration', 'sector')) {
                    $table->string('sector', 255)->after('company_name');
                }
                
                // Make address nullable
                if (Schema::hasColumn('elevateregistration', 'address')) {
                    $table->text('address')->nullable()->change();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('elevateregistration')) {
            Schema::table('elevateregistration', function (Blueprint $table) {
                if (Schema::hasColumn('elevateregistration', 'sector')) {
                    $table->dropColumn('sector');
                }
                
                // Revert address to not nullable
                if (Schema::hasColumn('elevateregistration', 'address')) {
                    $table->text('address')->nullable(false)->change();
                }
            });
        }
    }
};
