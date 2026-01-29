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
        Schema::table('ticket_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('ticket_orders', 'cgst_rate')) {
                $table->decimal('cgst_rate', 5, 2)->nullable()->after('gst_total');
            }
            if (!Schema::hasColumn('ticket_orders', 'cgst_total')) {
                $table->decimal('cgst_total', 10, 2)->default(0)->after('cgst_rate');
            }
            if (!Schema::hasColumn('ticket_orders', 'sgst_rate')) {
                $table->decimal('sgst_rate', 5, 2)->nullable()->after('cgst_total');
            }
            if (!Schema::hasColumn('ticket_orders', 'sgst_total')) {
                $table->decimal('sgst_total', 10, 2)->default(0)->after('sgst_rate');
            }
            if (!Schema::hasColumn('ticket_orders', 'igst_rate')) {
                $table->decimal('igst_rate', 5, 2)->nullable()->after('sgst_total');
            }
            if (!Schema::hasColumn('ticket_orders', 'igst_total')) {
                $table->decimal('igst_total', 10, 2)->default(0)->after('igst_rate');
            }
            if (!Schema::hasColumn('ticket_orders', 'gst_type')) {
                $table->enum('gst_type', ['cgst_sgst', 'igst'])->nullable()->after('igst_total');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_orders', function (Blueprint $table) {
            $table->dropColumn(['cgst_rate', 'cgst_total', 'sgst_rate', 'sgst_total', 'igst_rate', 'igst_total', 'gst_type']);
        });
    }
};
