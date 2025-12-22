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
        // Add foreign key from sponsorships to invoices
        if (Schema::hasTable('sponsorships') && Schema::hasTable('invoices')) {
            Schema::table('sponsorships', function (Blueprint $table) {
                $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            });
        }

        // Add foreign key from invoices to sponsorships
        if (Schema::hasTable('invoices') && Schema::hasTable('sponsorships')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->foreign('sponsorship_id')->references('id')->on('sponsorships')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('sponsorships')) {
            Schema::table('sponsorships', function (Blueprint $table) {
                $table->dropForeign(['invoice_id']);
            });
        }

        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropForeign(['sponsorship_id']);
            });
        }
    }
};

