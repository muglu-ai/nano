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
        // Add status and cancellation fields to complimentary_delegates
        if (Schema::hasTable('complimentary_delegates')) {
            Schema::table('complimentary_delegates', function (Blueprint $table) {
                if (!Schema::hasColumn('complimentary_delegates', 'status')) {
                    $table->enum('status', ['pending', 'accepted', 'cancelled'])->default('pending')->after('token');
                }
                if (!Schema::hasColumn('complimentary_delegates', 'cancelled_at')) {
                    $table->timestamp('cancelled_at')->nullable()->after('status');
                }
                if (!Schema::hasColumn('complimentary_delegates', 'cancelled_by')) {
                    $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null')->after('cancelled_at');
                }
                try {
                    $table->index('status');
                } catch (\Exception $e) {
                }
            });
        }

        // Add status and cancellation fields to stall_manning
        if (Schema::hasTable('stall_manning')) {
            Schema::table('stall_manning', function (Blueprint $table) {
                if (!Schema::hasColumn('stall_manning', 'status')) {
                    $table->enum('status', ['pending', 'accepted', 'cancelled'])->default('pending')->after('token');
                }
                if (!Schema::hasColumn('stall_manning', 'cancelled_at')) {
                    $table->timestamp('cancelled_at')->nullable()->after('status');
                }
                if (!Schema::hasColumn('stall_manning', 'cancelled_by')) {
                    $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null')->after('cancelled_at');
                }
                try {
                    $table->index('status');
                } catch (\Exception $e) {
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complimentary_delegates', function (Blueprint $table) {
            $table->dropForeign(['cancelled_by']);
            $table->dropIndex(['status']);
            $table->dropColumn(['status', 'cancelled_at', 'cancelled_by']);
        });

        Schema::table('stall_manning', function (Blueprint $table) {
            $table->dropForeign(['cancelled_by']);
            $table->dropIndex(['status']);
            $table->dropColumn(['status', 'cancelled_at', 'cancelled_by']);
        });
    }
};
