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
        Schema::table('ticket_registrations', function (Blueprint $table) {
            if (!Schema::hasColumn('ticket_registrations', 'subcategory_id')) {
                $table->unsignedBigInteger('subcategory_id')->nullable()->after('registration_category_id');
                $table->foreign('subcategory_id')
                      ->references('id')
                      ->on('ticket_subcategories')
                      ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_registrations', function (Blueprint $table) {
            if (Schema::hasColumn('ticket_registrations', 'subcategory_id')) {
                $table->dropForeign(['subcategory_id']);
                $table->dropColumn('subcategory_id');
            }
        });
    }
};
