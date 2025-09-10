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
        //
        Schema::table('payments', function (Blueprint $table) {
            // Adding new columns
            $table->decimal('amount_paid', 10, 2)->nullable()->after('amount');
            $table->string('currency', 10)->nullable()->after('payment_date');
            $table->text('rejection_reason')->nullable()->after('status');
            $table->string('receipt_image')->nullable()->after('rejection_reason');
            // Adding foreign key constraint for user_id
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
