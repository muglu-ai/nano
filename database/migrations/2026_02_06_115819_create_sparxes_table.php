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
        Schema::create('sparxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('registered_id')->unique()->index();  
            // $table->unique(['registered_id', 'event_year'], 'unique_sparx_per_user_per_year');
            $table->char('uuid', 35)->nullable();
            $table->unsignedBigInteger('event_id')->nullable()->index();
            $table->string('event_year', 10)->nullable()->index();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // 1. Applicant / Person
            $table->string('name', 120)->nullable(false);
            $table->string('designation', 100)->nullable(false);
            $table->string('organization', 150)->nullable(false);

            // Contact Information
            $table->string('email', 150)->nullable(false)->index();   // here we are not adding unique constraint 
            $table->string('phone_country_code', 5)->nullable();
            $table->string('phone_number', 20);
            $table->string('phone_full', 30)->nullable();

            // Address Information
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100);
            $table->string('postal_code', 10)->nullable();


            // Startup / Idea Core
            $table->string('startup_idea_name', 120)->nullable(false);
            $table->string('website', 255)->nullable();               // without protocol

            $table->string('sector', 80)->nullable();

            $table->text('idea_description')->nullable(false);       // was resource_req – renamed!
            $table->text('products')->nullable(false);
            $table->text('key_successes')->nullable(false);

            $table->string('potential_market_size', 120)->nullable(false);
            $table->integer('company_size_employees')->nullable(false)->default(0);

            // Registration status
            $table->boolean('is_registered')->default(false);
            $table->date('registration_date')->nullable();

            // Consent & meta
            $table->boolean('consent_given')->default(true);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            // Workflow
            $table->string('status', 30)
                ->default('draft')
                ->comment('draft, submitted, reviewed, rejected, accepted');

            //foreign key 
            $table->foreign('event_id')->references('id')->on('events')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sparxes');
    }
};