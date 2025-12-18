<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Only add columns if they don't already exist (handles imported schema)
        if (Schema::hasTable('applications')) {
            Schema::table('applications', function (Blueprint $table) {
                if (!Schema::hasColumn('applications', 'participant_type')) {
                    $table->string('participant_type', 255)->nullable();
                }

                if (!Schema::hasColumn('applications', 'interested_sqm')) {
                    $table->string('interested_sqm', 25)->nullable();
                }

                if (!Schema::hasColumn('applications', 'product_groups')) {
                    $table->string('product_groups', 255)->nullable();
                }

                if (!Schema::hasColumn('applications', 'cancellation_terms')) {
                    $table->tinyInteger('cancellation_terms')->default(0);
                }

                if (!Schema::hasColumn('applications', 'region')) {
                    // region as string to store the region of the user
                    $table->string('region', 30)->nullable();
                }

                if (!Schema::hasColumn('applications', 'terms_accepted')) {
                    $table->tinyInteger('terms_accepted')->default(0);
                }

                if (!Schema::hasColumn('applications', 'semi_memberID')) {
                    // semi_memberID
                    $table->string('semi_memberID', 255)->nullable();
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('applications')) {
            Schema::table('applications', function (Blueprint $table) {
                if (Schema::hasColumn('applications', 'participant_type')) {
                    $table->dropColumn('participant_type');
                }
                if (Schema::hasColumn('applications', 'interested_sqm')) {
                    $table->dropColumn('interested_sqm');
                }
                if (Schema::hasColumn('applications', 'product_groups')) {
                    $table->dropColumn('product_groups');
                }
                if (Schema::hasColumn('applications', 'cancellation_terms')) {
                    $table->dropColumn('cancellation_terms');
                }
                if (Schema::hasColumn('applications', 'terms_accepted')) {
                    $table->dropColumn('terms_accepted');
                }
                if (Schema::hasColumn('applications', 'region')) {
                    $table->dropColumn('region');
                }
                if (Schema::hasColumn('applications', 'semi_memberID')) {
                    $table->dropColumn('semi_memberID');
                }
            });
        }
    }
};
