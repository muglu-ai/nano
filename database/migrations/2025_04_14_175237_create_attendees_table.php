<?php 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendeesTable extends Migration
{
    public function up(): void
    {
        Schema::create('attendees', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->string('badge_category')->nullable();
            $table->string('title', 25)->nullable();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('designation')->nullable();
            $table->string('company')->nullable();
            $table->string('address')->nullable();
            $table->foreignId('country')->nullable()->constrained('countries');
            $table->foreignId('state')->nullable()->constrained('states');
            $table->string('city')->nullable();
            $table->string('postal_code', 50)->nullable();
            $table->string('mobile', 50)->nullable();
            $table->string('email');
            $table->text('purpose')->nullable();
            $table->text('products')->nullable();
            $table->text('business_nature')->nullable();
            $table->string('job_function')->nullable();
            $table->string('job_category', 125)->nullable();
            $table->string('job_subcategory', 125)->nullable();
            $table->text('profile_picture')->nullable();
            $table->string('id_card_type', 125)->nullable();
            $table->string('id_card_number', 125)->nullable();
            $table->boolean('consent')->default(false);
            $table->datetime('created_at')->useCurrent();
            $table->datetime('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->text('qr_code_path')->nullable();
            $table->text('source')->nullable();
            $table->boolean('email_verified')->default(false);
            $table->string('email_verify_otp', 10)->nullable();
            $table->boolean('inaugural_session')->nullable();
            $table->string('registration_type', 125)->nullable();
            $table->text('event_days')->nullable();
            $table->string('other_job_category', 250)->nullable();
            $table->string('promotion_consent', 12)->nullable();
            $table->boolean('startup')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'active'])->default('pending');
            $table->boolean('inauguralConfirmation')->default(false);
            $table->string('approvedCate', 250)->nullable();
            $table->string('regId', 50)->nullable();
            $table->boolean('lunchStatus')->default(false);
            $table->text('approvedHistory')->nullable();
            $table->text('updatedBy')->nullable();
            $table->json('api_data')->nullable();
            $table->json('api_response')->nullable();
            $table->boolean('api_sent')->nullable();
            $table->boolean('emailSent')->default(false);
            $table->json('reminder')->nullable();

            // Indexes
            $table->index('country');
            $table->index('state');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendees');
    }
}
