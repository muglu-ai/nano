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
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                if (!Schema::hasColumn('invoices', 'invoice_no')) {
                    $table->string('invoice_no')->nullable();
                }
                if (!Schema::hasColumn('invoices', 'pending_amount')) {
                    $table->decimal('pending_amount', 10, 2)->default(0);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                if (Schema::hasColumn('invoices', 'invoice_no')) {
                    $table->dropColumn('invoice_no');
                }
                if (Schema::hasColumn('invoices', 'pending_amount')) {
                    $table->dropColumn('pending_amount');
                }
            });
        }
    }
};
