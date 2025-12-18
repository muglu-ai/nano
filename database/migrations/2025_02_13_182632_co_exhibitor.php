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
        if (Schema::hasTable('co_exhibitors')) {
            Schema::table('co_exhibitors', function (Blueprint $table) {
                if (!Schema::hasColumn('co_exhibitors', 'status')) {
                    $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // Admin approval
                }
                if (!Schema::hasColumn('co_exhibitors', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable(); // Link to user account
                }
                if (!Schema::hasColumn('co_exhibitors', 'allocated_passes')) {
                    $table->integer('allocated_passes')->default(0); // Pass allocation
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('co_exhibitors')) {
            Schema::table('co_exhibitors', function (Blueprint $table) {
                if (Schema::hasColumn('co_exhibitors', 'status')) {
                    $table->dropColumn('status');
                }
                if (Schema::hasColumn('co_exhibitors', 'user_id')) {
                    $table->dropColumn('user_id');
                }
                if (Schema::hasColumn('co_exhibitors', 'allocated_passes')) {
                    $table->dropColumn('allocated_passes');
                }
            });
        }
    }
};
