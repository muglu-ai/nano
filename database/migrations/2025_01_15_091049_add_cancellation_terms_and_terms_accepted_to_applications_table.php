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
    Schema::table('applications', function (Blueprint $table) {
       
        $table->string('participant_type', 255)->nullable();
        $table->string('interested_sqm', 25)->nullable();
        $table->string('product_groups', 255)->nullable();
        $table->tinyInteger('cancellation_terms')->default(0);
        //region as string to store the region of the user
        $table->string('region', 30)->nullable(); 

        $table->tinyInteger('terms_accepted')->default(0);
        //semi_memberID
        $table->string('semi_memberID', 255)->nullable();
    });
}

    public function down()
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('participant_type');
            $table->dropColumn('interested_sqm');
            $table->dropColumn('product_groups');
            $table->dropColumn('cancellation_terms');
            $table->dropColumn('terms_accepted');
            $table->dropColumn('region');
        });
    }
};
