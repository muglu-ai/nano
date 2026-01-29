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
        // Ticket Registrations - Company header + UTM tracking
        if (!Schema::hasTable('ticket_registrations')) {
            Schema::create('ticket_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('contact_id')->constrained('ticket_contacts')->onDelete('cascade');
            $table->string('company_name');
            $table->string('company_country')->nullable();
            $table->string('company_state')->nullable();
            $table->string('company_city')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('industry_sector')->nullable();
            $table->string('organisation_type')->nullable();
            $table->foreignId('registration_category_id')->nullable()->constrained('ticket_registration_categories')->onDelete('set null');
            $table->boolean('gst_required')->default(false);
            $table->string('gstin')->nullable();
            $table->string('gst_legal_name')->nullable();
            $table->text('gst_address')->nullable();
            $table->string('gst_state')->nullable();
            $table->string('nationality')->nullable();
            // UTM tracking
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('utm_content')->nullable();
            // Source attribution
            $table->string('ref_source_type')->nullable(); // 'association', 'admin_invite', 'promo', null
            $table->unsignedBigInteger('ref_source_id')->nullable(); // ID of association, invite, etc.
            $table->timestamps();

            $table->index(['event_id', 'contact_id']);
            $table->index('ref_source_type');
            });
        }

        // Ticket Delegates - Unlimited delegates per registration
        if (!Schema::hasTable('ticket_delegates')) {
            Schema::create('ticket_delegates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('ticket_registrations')->onDelete('cascade');
            $table->string('salutation')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('job_title')->nullable();
            $table->timestamps();

            $table->index(['registration_id', 'email']);
            });
        }

        // Ticket Delegate Assignments - Delegate → ticket selection snapshot
        if (!Schema::hasTable('ticket_delegate_assignments')) {
            Schema::create('ticket_delegate_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delegate_id')->constrained('ticket_delegates')->onDelete('cascade');
            $table->foreignId('ticket_type_id')->constrained('ticket_types')->onDelete('cascade');
            $table->foreignId('subcategory_id')->nullable()->constrained('ticket_subcategories')->onDelete('set null');
            $table->json('day_access_snapshot_json')->nullable(); // Snapshot of allowed days at time of assignment
            $table->decimal('price_snapshot', 10, 2); // Price at time of assignment
            $table->string('pricing_type_snapshot')->nullable(); // 'early_bird' or 'regular'
            $table->timestamps();

            $table->index('delegate_id');
            });
        }

        // Tickets - Issued tickets (one per delegate)
        if (!Schema::hasTable('tickets')) {
            Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('delegate_id')->constrained('ticket_delegates')->onDelete('cascade');
            $table->foreignId('ticket_type_id')->constrained('ticket_types')->onDelete('cascade');
            $table->enum('status', ['pending', 'issued', 'cancelled', 'upgraded'])->default('pending');
            $table->json('access_snapshot_json')->nullable(); // Snapshot of day access at issuance
            $table->string('source_type')->nullable(); // 'regular', 'association', 'admin_invite', 'promo'
            $table->unsignedBigInteger('source_id')->nullable(); // ID of association, invite, etc.
            $table->timestamps();

            $table->index(['event_id', 'status']);
            $table->index('delegate_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('ticket_delegate_assignments');
        Schema::dropIfExists('ticket_delegates');
        Schema::dropIfExists('ticket_registrations');
    }
};

