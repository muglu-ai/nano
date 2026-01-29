<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * User-selected subcategory when registering (e.g. Students under Standard).
     */
    public function up(): void
    {
        Schema::table('ticket_registrations', function (Blueprint $table) {
            if (!Schema::hasColumn('ticket_registrations', 'subcategory_id')) {
                $table->foreignId('subcategory_id')->nullable()->after('registration_category_id')->constrained('ticket_subcategories')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_registrations', function (Blueprint $table) {
            $table->dropForeign(['subcategory_id']);
        });
    }
};
