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
        if (Schema::hasTable('applications')) {
            Schema::table('applications', function (Blueprint $table) {
                if (!Schema::hasColumn('applications', 'pavilion_id')) {
                    $table->integer('pavilion_id')->nullable();
                }
                if (!Schema::hasColumn('applications', 'sponsorship_item_id')) {
                    $table->string('sponsorship_item_id')->nullable();
                }
                if (!Schema::hasColumn('applications', 'sponsorship_count')) {
                    $table->string('sponsorship_count')->nullable();
                }
                if (!Schema::hasColumn('applications', 'spon_discount_eligible')) {
                    $table->boolean('spon_discount_eligible')->default(false);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('applications')) {
            Schema::table('applications', function (Blueprint $table) {
                if (Schema::hasColumn('applications', 'pavilion_id')) {
                    $table->dropColumn('pavilion_id');
                }
                if (Schema::hasColumn('applications', 'sponsorship_item_id')) {
                    $table->dropColumn('sponsorship_item_id');
                }
                if (Schema::hasColumn('applications', 'sponsorship_count')) {
                    $table->dropColumn('sponsorship_count');
                }
                if (Schema::hasColumn('applications', 'spon_discount_eligible')) {
                    $table->dropColumn('spon_discount_eligible');
                }
            });
        }
    }
};
