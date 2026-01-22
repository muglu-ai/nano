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
        Schema::table('ticket_order_items', function (Blueprint $table) {
            $table->decimal('cgst_rate', 5, 2)->nullable()->after('gst_amount');
            $table->decimal('cgst_amount', 10, 2)->default(0)->after('cgst_rate');
            $table->decimal('sgst_rate', 5, 2)->nullable()->after('cgst_amount');
            $table->decimal('sgst_amount', 10, 2)->default(0)->after('sgst_rate');
            $table->decimal('igst_rate', 5, 2)->nullable()->after('sgst_amount');
            $table->decimal('igst_amount', 10, 2)->default(0)->after('igst_rate');
            $table->enum('gst_type', ['cgst_sgst', 'igst'])->nullable()->after('igst_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_order_items', function (Blueprint $table) {
            $table->dropColumn(['cgst_rate', 'cgst_amount', 'sgst_rate', 'sgst_amount', 'igst_rate', 'igst_amount', 'gst_type']);
        });
    }
};
