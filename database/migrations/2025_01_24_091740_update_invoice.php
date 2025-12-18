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
                if (!Schema::hasColumn('invoices', 'discount_per')) {
                    $table->float('discount_per')->nullable();
                }
                if (!Schema::hasColumn('invoices', 'discount')) {
                    $table->float('discount')->nullable();
                }
                if (!Schema::hasColumn('invoices', 'gst')) {
                    $table->float('gst')->nullable();
                }
                if (!Schema::hasColumn('invoices', 'processing_charges')) {
                    $table->float('processing_charges')->nullable();
                }
                if (!Schema::hasColumn('invoices', 'price')) {
                    $table->float('price')->nullable();
                }
                if (!Schema::hasColumn('invoices', 'total_final_price')) {
                    $table->float('total_final_price')->nullable();
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
                if (Schema::hasColumn('invoices', 'discount_per')) {
                    $table->dropColumn('discount_per');
                }
                if (Schema::hasColumn('invoices', 'discount')) {
                    $table->dropColumn('discount');
                }
                if (Schema::hasColumn('invoices', 'gst')) {
                    $table->dropColumn('gst');
                }
                if (Schema::hasColumn('invoices', 'processing_charges')) {
                    $table->dropColumn('processing_charges');
                }
                if (Schema::hasColumn('invoices', 'price')) {
                    $table->dropColumn('price');
                }
                if (Schema::hasColumn('invoices', 'total_final_price')) {
                    $table->dropColumn('total_final_price');
                }
            });
        }
    }
};
