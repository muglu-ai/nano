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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->char('uuid', 35)->nullable();
            $table->string('file_path')->nullable();
            $table->integer('how_old_startup')->nullable();
            $table->string('company_name');
            $table->string('address')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('city_id')->nullable();
            $table->foreignId('state_id')->nullable()->constrained('states');
            $table->foreignId('country_id')->nullable()->constrained('countries');
            $table->string('landline')->nullable();
            $table->string('company_email');
            $table->string('website')->nullable();
            $table->string('main_product_category', 125)->nullable();
            $table->foreignId('headquarters_country_id')->nullable()->constrained('countries');
            $table->string('sector_id')->nullable();
            $table->string('subSector')->nullable();
            $table->string('type_of_business')->nullable();
            $table->string('comments')->nullable();
            $table->boolean('participated_previous')->default(false);
            $table->boolean('semi_member')->default(false);
            $table->string('stall_category')->nullable();
            $table->string('boothDescription')->nullable();
            $table->integer('booth_count')->nullable();
            $table->string('fascia_name')->nullable();
            $table->enum('payment_currency', ['EUR', 'INR'])->default('INR');
            $table->enum('status', ['initiated', 'submitted', 'approved', 'rejected'])->default('initiated');
            $table->string('application_id')->unique();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->string('participant_type')->nullable();
            $table->string('interested_sqm', 125)->nullable();
            $table->string('product_groups', 500)->nullable();
            $table->tinyInteger('cancellation_terms')->default(0);
            $table->string('region', 30)->nullable();
            $table->tinyInteger('terms_accepted')->default(0);
            $table->string('semi_memberID')->nullable();
            $table->string('submission_status')->default('in progress');
            $table->string('salesPerson', 125)->nullable();
            $table->timestamp('approved_date')->nullable();
            $table->foreignId('event_id')->default(1)->constrained('events');
            $table->foreignId('billing_country_id')->nullable()->constrained('countries')->onDelete('set null');
            $table->string('gst_no')->nullable();
            $table->string('pan_no')->nullable();
            $table->string('tan_no')->nullable();
            $table->string('participation_type')->nullable();
            $table->boolean('gst_compliance')->default(false);
            $table->string('certificate')->nullable();
            $table->date('submission_date')->nullable();
            $table->date('approval_date')->nullable();
            $table->string('approved_by')->nullable();
            $table->boolean('is_pavilion')->default(false);
            $table->boolean('has_sponsorship')->default(false);
            $table->tinyInteger('withdraw_title')->default(0);
            $table->string('allocated_sqm', 125)->nullable();
            $table->integer('pavilion_id')->nullable();
            $table->string('sponsorship_item_id')->nullable();
            $table->string('sponsorship_count')->nullable();
            $table->string('application_type', 125)->default('exhibitor');
            $table->boolean('spon_discount_eligible')->default(false);
            $table->date('rejected_date')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->string('stallNumber', 50)->nullable();
            $table->string('zone')->nullable();
            $table->string('assoc_mem', 125)->nullable();
            $table->integer('country_name')->nullable();
            $table->string('pref_location', 125)->nullable();
            $table->boolean('membership_verified')->nullable();
            $table->json('cart_data')->nullable();
            $table->tinyInteger('sponsor_only')->default(0);
            $table->boolean('coex_terms_accepted')->default(false);
            $table->text('remarks')->nullable();
            $table->text('logo_link')->nullable();
            $table->boolean('userActive')->default(false);
            $table->integer('companyYears')->nullable();
            $table->string('exhibitorType')->nullable();
            $table->string('RegSource', 250)->nullable();
            $table->boolean('declarationStatus')->default(false);
            $table->string('hallNo', 50)->nullable();
            $table->string('pavilionName')->nullable();

            // Indexes
            $table->index('billing_country_id');
            $table->index('country_id');
            $table->index('event_id');
            $table->index('headquarters_country_id');
            $table->index('state_id');
            $table->index('user_id');
            $table->index('city_id');
            $table->index('sector_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
