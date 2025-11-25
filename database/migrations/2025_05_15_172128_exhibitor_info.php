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
        Schema::create('exhibitors_info', function (Blueprint $table) {
            $table->id();
            $table->integer('api_status')->nullable();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->string('fascia_name');
            $table->string('company_name')->nullable();
            $table->string('sector')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('zip_code', 20)->nullable();
            $table->string('contact_person')->nullable();
            $table->string('designation')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('telPhone')->nullable();
            $table->string('logo')->nullable();
            $table->text('address')->nullable();
            $table->text('description')->nullable();
            $table->text('website')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('instagram')->nullable();
            $table->string('facebook')->nullable();
            $table->string('youtube')->nullable();
            $table->boolean('submission_status')->default(false);
            $table->string('category', 50)->nullable();
            $table->text('api_message')->nullable();
            $table->string('apiExhibitorId', 150)->nullable();
            $table->timestamps();

            // Indexes
            $table->index('application_id');
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
