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
        // Create applications_delete table
        if (!Schema::hasTable('applications_delete')) {
            Schema::create('applications_delete', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('company_name');
            $table->string('address');
            $table->string('postal_code');
            $table->string('city_id')->nullable();
            $table->foreignId('state_id')->constrained('states')->onDelete('cascade');
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            $table->string('landline');
            $table->string('company_email');
            $table->string('website')->nullable();
            $table->string('main_product_category', 125)->nullable();
            $table->foreignId('headquarters_country_id')->constrained('countries')->onDelete('cascade');
            $table->string('sector_id')->nullable();
            $table->string('type_of_business');
            $table->string('comments')->nullable();
            $table->boolean('participated_previous')->default(false);
            $table->boolean('semi_member')->default(false);
            $table->enum('stall_category', ['Shell Scheme', 'Bare Space']);
            $table->integer('booth_count')->nullable();
            $table->enum('payment_currency', ['EUR', 'INR']);
            $table->enum('status', ['initiated', 'submitted', 'approved', 'rejected']);
            $table->string('application_id');
            $table->timestamps();
            $table->string('participant_type')->nullable();
            $table->string('interested_sqm', 25)->nullable();
            $table->string('product_groups', 500)->nullable();
            $table->boolean('cancellation_terms')->default(false);
            $table->string('region', 30)->nullable();
            $table->boolean('terms_accepted')->default(false);
            $table->string('semi_memberID')->nullable();
            $table->string('submission_status')->default('in progress');
            $table->timestamp('approved_date')->nullable();
            $table->unsignedBigInteger('event_id')->default(1);
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreignId('billing_country_id')->nullable()->constrained('countries')->onDelete('set null');
            $table->string('gst_no')->nullable();
            $table->string('pan_no')->nullable();
            $table->string('tan_no')->nullable();
            $table->string('participation_type')->nullable();
            $table->boolean('gst_compliance')->default(false);
            $table->string('certificate')->nullable();
            $table->date('submission_date')->nullable();
            $table->date('approval_date')->nullable();
            $table->boolean('is_pavilion')->default(false);
            $table->integer('allocated_sqm')->nullable();
            $table->integer('pavilion_id')->nullable();
            $table->string('sponsorship_item_id')->nullable();
            $table->string('sponsorship_count')->nullable();
            $table->string('application_type', 125)->default('exhibitor');
            $table->date('rejected_date')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->string('stallNumber', 50)->nullable();
            $table->string('zone')->nullable();
            $table->string('assoc_mem', 125)->nullable();
            $table->integer('country_name')->nullable();
            $table->string('pref_location', 125)->nullable();
            $table->boolean('membership_verified')->nullable();

                // Indexes
                $table->index('user_id');
                $table->index('state_id');
                $table->index('country_id');
                $table->index('headquarters_country_id');
                $table->index('event_id');
            });

        // Create billing_details_delete table
        Schema::create('billing_details_delete', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->string('billing_company');
            $table->string('contact_name');
            $table->string('email');
            $table->string('phone');
            $table->string('address');
            $table->string('gst_id')->nullable();
            $table->string('city_id')->nullable();
            $table->foreignId('state_id')->constrained('states')->onDelete('cascade');
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            $table->string('postal_code');
            $table->boolean('same_as_basic'); // NOT NULL in SQL, no default
            $table->timestamps();

                // Indexes
                $table->index('application_id');
                $table->index('state_id');
                $table->index('country_id');
            });
        }

        // Create event_contacts_delete table
        if (!Schema::hasTable('event_contacts_delete')) {
            Schema::create('event_contacts_delete', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->string('salutation', 25); // NOT NULL in SQL
            $table->string('first_name'); // NOT NULL in SQL
            $table->string('last_name'); // NOT NULL in SQL
            $table->string('job_title'); // NOT NULL in SQL
            $table->string('email'); // NOT NULL in SQL
            $table->string('contact_number'); // NOT NULL in SQL
            $table->string('secondary_email')->nullable();
            $table->timestamps();
            $table->string('designation')->nullable();

                // Indexes
                $table->index('application_id');
            });
        }

        // Create secondary_event_contacts_delete table
        if (!Schema::hasTable('secondary_event_contacts_delete')) {
            Schema::create('secondary_event_contacts_delete', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->string('salutation', 25)->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('job_title')->nullable();
            $table->string('email')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('secondary_email')->nullable();
            $table->string('designation')->nullable();
            $table->timestamps();

                // Indexes
                $table->index('application_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('secondary_event_contacts_delete');
        Schema::dropIfExists('event_contacts_delete');
        Schema::dropIfExists('billing_details_delete');
        Schema::dropIfExists('applications_delete');
    }
};

