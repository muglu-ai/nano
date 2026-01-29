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
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'igst_rate')) {
                $table->decimal('igst_rate', 5, 2)->nullable()->after('gst');
            }
            if (!Schema::hasColumn('invoices', 'igst_amount')) {
                $table->decimal('igst_amount', 15, 2)->nullable()->after('igst_rate');
            }
            if (!Schema::hasColumn('invoices', 'cgst_rate')) {
                $table->decimal('cgst_rate', 5, 2)->nullable()->after('igst_amount');
            }
            if (!Schema::hasColumn('invoices', 'cgst_amount')) {
                $table->decimal('cgst_amount', 15, 2)->nullable()->after('cgst_rate');
            }
            if (!Schema::hasColumn('invoices', 'sgst_rate')) {
                $table->decimal('sgst_rate', 5, 2)->nullable()->after('cgst_amount');
            }
            if (!Schema::hasColumn('invoices', 'sgst_amount')) {
                $table->decimal('sgst_amount', 15, 2)->nullable()->after('sgst_rate');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['igst_rate', 'igst_amount', 'cgst_rate', 'cgst_amount', 'sgst_rate', 'sgst_amount']);
        });
    }
};
