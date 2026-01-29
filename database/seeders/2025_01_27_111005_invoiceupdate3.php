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
        // partial payment percentage column
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('partial_payment_percentage', 5, 2)->nullable()->after('price');
            $table->bigInteger('application_no')->unsigned()->nullable()->after('price');
            $table->foreign('application_no')->references('application_id')->on('applications');

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
