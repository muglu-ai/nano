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
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                if (!Schema::hasColumn('payments', 'amount_paid')) {
                    $table->decimal('amount_paid', 10, 2)->nullable()->after('amount');
                }
                if (!Schema::hasColumn('payments', 'currency')) {
                    $table->string('currency', 10)->nullable()->after('payment_date');
                }
                if (!Schema::hasColumn('payments', 'rejection_reason')) {
                    $table->text('rejection_reason')->nullable()->after('status');
                }
                if (!Schema::hasColumn('payments', 'receipt_image')) {
                    $table->string('receipt_image')->nullable()->after('rejection_reason');
                }
                if (!Schema::hasColumn('payments', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                if (Schema::hasColumn('payments', 'amount_paid')) {
                    $table->dropColumn('amount_paid');
                }
                if (Schema::hasColumn('payments', 'currency')) {
                    $table->dropColumn('currency');
                }
                if (Schema::hasColumn('payments', 'rejection_reason')) {
                    $table->dropColumn('rejection_reason');
                }
                if (Schema::hasColumn('payments', 'receipt_image')) {
                    $table->dropColumn('receipt_image');
                }
                if (Schema::hasColumn('payments', 'user_id')) {
                    $table->dropForeign(['user_id']);
                    $table->dropColumn('user_id');
                }
            });
        }
    }
};
