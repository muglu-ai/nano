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
        // Payment Gateway Response
        if (!Schema::hasTable('payment_gateway_response')) {
            Schema::create('payment_gateway_response', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id')->nullable();
                $table->string('order_id', 50);
                $table->string('payment_id', 100)->nullable();
                $table->string('invoice_id', 100)->nullable();
                $table->string('currency', 10);
                $table->string('gateway', 50)->nullable();
                $table->decimal('amount', 10, 2);
                $table->decimal('amount_received', 10, 2)->nullable();
                $table->string('transaction_id', 100)->nullable();
                $table->string('reference_id', 100)->nullable();
                $table->string('email');
                $table->string('status', 120)->nullable();
                $table->json('response_json')->nullable();
                $table->json('merchant_data')->nullable();
                $table->string('bank_ref_no', 50)->nullable();
                $table->string('trans_date', 250)->nullable();
                $table->string('payment_method', 50)->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            });
        }

        // OTPs
        if (!Schema::hasTable('otps')) {
            Schema::create('otps', function (Blueprint $table) {
                $table->id();
                $table->string('identifier');
                $table->string('otp');
                $table->boolean('verified')->default(false);
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }

        // Outbound Requests
        if (!Schema::hasTable('outbound_requests')) {
            Schema::create('outbound_requests', function (Blueprint $table) {
                $table->id();
                $table->string('endpoint');
                $table->char('idempotency_key', 36)->unique();
                $table->text('reg_id')->nullable();
                $table->json('payload');
                $table->string('status')->default('queued');
                $table->unsignedTinyInteger('attempts')->default(0);
                $table->integer('response_code')->nullable();
                $table->text('response_body')->nullable();
                $table->text('last_error')->nullable();
                $table->timestamp('responded_at')->nullable();
                $table->timestamps();
            });
        }

        // Requirements Billings
        if (!Schema::hasTable('requirements_billings')) {
            Schema::create('requirements_billings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
                $table->string('invoice_no')->nullable();
                $table->string('billing_company')->nullable();
                $table->string('billing_name');
                $table->string('billing_email');
                $table->string('billing_phone', 50);
                $table->string('gst_no', 50)->nullable();
                $table->string('pan_no', 50)->nullable();
                $table->text('billing_address');
                $table->string('billing_city')->nullable();
                $table->foreignId('country_id')->constrained('countries');
                $table->foreignId('state_id')->constrained('states');
                $table->string('zipcode', 50);
                $table->timestamps();

                // Indexes
                $table->index('invoice_id');
                $table->index('country_id');
                $table->index('state_id');
            });
        }

        // Lead Retrieval User
        if (!Schema::hasTable('lead_retrieval_user')) {
            Schema::create('lead_retrieval_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users');
                $table->string('name', 150);
                $table->string('email', 150)->unique();
                $table->string('mobile', 20);
                $table->string('designation', 100)->nullable();
                $table->string('company_name', 150)->nullable();
                $table->timestamp('registered_at')->useCurrent();

                // Indexes
                $table->index('user_id');
            });
        }

        // Attendee Logs
        if (!Schema::hasTable('attendee_logs')) {
            Schema::create('attendee_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('attendee_id')->constrained('attendees');
                $table->string('name');
                $table->string('email')->nullable();
                $table->json('data')->nullable();
                $table->timestamp('deleted_at')->nullable();
                $table->timestamps();
            });
        }

        // Exhibitor Products
        if (!Schema::hasTable('exhibitor_products')) {
            Schema::create('exhibitor_products', function (Blueprint $table) {
                $table->id();
                $table->foreignId('application_id')->constrained('applications');
                $table->string('product_name');
                $table->string('product_image')->nullable();
                $table->text('description')->nullable();
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
        }

        // Exhibitor Press Releases
        if (!Schema::hasTable('exhibitor_press_releases')) {
            Schema::create('exhibitor_press_releases', function (Blueprint $table) {
                $table->id();
                $table->foreignId('exhibitor_id')->constrained('exhibitors_info')->onDelete('cascade');
                $table->string('title');
                $table->string('file')->nullable();
                $table->text('summary')->nullable();
                $table->text('link')->nullable();
                $table->timestamps();

                // Indexes
                $table->index('exhibitor_id');
            });
        }

        // Exhibitor Feedback
        if (!Schema::hasTable('exhibitor_feedback')) {
            Schema::create('exhibitor_feedback', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->comment('Optional: user ID if logged in');
                $table->string('name');
                $table->string('email');
                $table->string('company_name')->nullable();
                $table->string('phone', 20)->nullable();
                $table->unsignedTinyInteger('event_rating')->comment('Rating from 1 to 5');
                $table->unsignedTinyInteger('portal_rating')->comment('Rating from 1 to 5');
                $table->unsignedTinyInteger('overall_experience_rating')->nullable()->comment('Rating from 1 to 5');
                $table->text('what_liked_most')->nullable();
                $table->text('what_could_be_improved')->nullable();
                $table->text('additional_comments')->nullable();
                $table->enum('would_recommend', ['yes', 'no', 'maybe'])->nullable();
                $table->unsignedTinyInteger('event_organization_rating')->nullable()->comment('Rating from 1 to 5');
                $table->unsignedTinyInteger('venue_rating')->nullable()->comment('Rating from 1 to 5');
                $table->unsignedTinyInteger('networking_opportunities_rating')->nullable()->comment('Rating from 1 to 5');
                $table->timestamps();

                // Indexes
                $table->index('user_id');
                $table->index('email');
                $table->index('created_at');
            });
        }

        // Blocked Slots
        if (!Schema::hasTable('blocked_slots')) {
            Schema::create('blocked_slots', function (Blueprint $table) {
                $table->id();
                $table->foreignId('meeting_room_id')->nullable()->constrained('meeting_rooms')->onDelete('cascade');
                $table->date('date');
                $table->enum('time_slot', ['morning', 'afternoon']);
                $table->string('reason');
                $table->timestamps();

                // Unique constraint
                $table->unique(['meeting_room_id', 'date', 'time_slot'], 'unique_room_date_slot');
            });
        }

        // Payment Receipts (if not exists)
        if (!Schema::hasTable('payment_receipts')) {
            Schema::create('payment_receipts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('invoice_id')->constrained('invoices');
                $table->string('receipt_path');
                $table->string('status')->default('pending');
                $table->text('remarks')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateway_response');
        Schema::dropIfExists('otps');
        Schema::dropIfExists('outbound_requests');
        Schema::dropIfExists('requirements_billings');
        Schema::dropIfExists('lead_retrieval_user');
        Schema::dropIfExists('attendee_logs');
        Schema::dropIfExists('exhibitor_products');
        Schema::dropIfExists('exhibitor_press_releases');
        Schema::dropIfExists('exhibitor_feedback');
        Schema::dropIfExists('blocked_slots');
        Schema::dropIfExists('payment_receipts');
    }
};

