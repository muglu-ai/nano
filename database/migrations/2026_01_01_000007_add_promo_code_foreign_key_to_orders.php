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
        // Add foreign key constraint for promo_code_id after ticket_promo_codes table is created
        if (Schema::hasTable('ticket_orders') && Schema::hasTable('ticket_promo_codes')) {
            try {
                Schema::table('ticket_orders', function (Blueprint $table) {
                    $table->foreign('promo_code_id')
                        ->references('id')
                        ->on('ticket_promo_codes')
                        ->onDelete('set null');
                });
            } catch (\Exception $e) {
                // Foreign key might already exist, skip
                if (strpos($e->getMessage(), 'Duplicate key name') === false && 
                    strpos($e->getMessage(), 'already exists') === false) {
                    throw $e;
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_orders', function (Blueprint $table) {
            $table->dropForeign(['promo_code_id']);
        });
    }
};

