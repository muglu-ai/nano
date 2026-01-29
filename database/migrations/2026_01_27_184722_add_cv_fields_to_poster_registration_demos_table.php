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
        Schema::table('poster_registration_demos', function (Blueprint $table) {
            if (!Schema::hasColumn('poster_registration_demos', 'lead_auth_cv_path')) {
                $table->string('lead_auth_cv_path')->nullable()->after('extended_abstract_original_name');
            }
            if (!Schema::hasColumn('poster_registration_demos', 'lead_auth_cv_original_name')) {
                $table->string('lead_auth_cv_original_name')->nullable()->after('lead_auth_cv_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('poster_registration_demos', function (Blueprint $table) {
            $table->dropColumn(['lead_auth_cv_path', 'lead_auth_cv_original_name']);
        });
    }
};
