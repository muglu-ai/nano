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
        // Ticket Events Config - Event-level behavior
        if (!Schema::hasTable('ticket_events_config')) {
            Schema::create('ticket_events_config', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->unique()->constrained('events')->onDelete('cascade');
            $table->enum('auth_policy', ['guest', 'otp_required', 'login_required'])->default('guest');
            $table->enum('selection_mode', ['same_ticket', 'per_delegate'])->default('same_ticket');
            $table->boolean('allow_subcategory')->default(true);
            $table->boolean('allow_day_select')->default(false);
            $table->json('email_cc_json')->nullable(); // Array of email addresses
            $table->string('receipt_pattern')->nullable(); // e.g., "TKT-{event}-{year}-{seq}"
            $table->boolean('is_active')->default(false); // Admin can disable registration
            $table->timestamps();
            });
        }

        // Event Days - Explicit event days
        if (!Schema::hasTable('event_days')) {
            Schema::create('event_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('label'); // e.g., "Day 1", "Day 2", "VIP Day"
            $table->date('date');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['event_id', 'date']);
            $table->index(['event_id', 'sort_order']);
            });
        }

        // Ticket Registration Categories - Registration categories (separate from ticket type)
        if (!Schema::hasTable('ticket_registration_categories')) {
            Schema::create('ticket_registration_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('name'); // Delegate/Visitor/VIP/Student
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['event_id', 'is_active']);
            });
        }

        // Ticket Categories - Ticket grouping
        if (!Schema::hasTable('ticket_categories')) {
            Schema::create('ticket_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('name'); // Delegate/VIP/Workshop
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['event_id', 'sort_order']);
            });
        }

        // Ticket Subcategories - Sub grouping
        if (!Schema::hasTable('ticket_subcategories')) {
            Schema::create('ticket_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('ticket_categories')->onDelete('cascade');
            $table->string('name'); // Member/Non-member/Student
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['category_id', 'sort_order']);
            });
        }

        // Ticket Types - Sellable ticket types
        if (!Schema::hasTable('ticket_types')) {
            Schema::create('ticket_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('ticket_categories')->onDelete('cascade');
            $table->foreignId('subcategory_id')->nullable()->constrained('ticket_subcategories')->onDelete('set null');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('early_bird_price', 10, 2)->nullable();
            $table->decimal('regular_price', 10, 2);
            $table->date('early_bird_end_date')->nullable();
            $table->integer('capacity')->nullable(); // null for unlimited
            $table->timestamp('sale_start_at')->nullable();
            $table->timestamp('sale_end_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('early_bird_reminder_sent')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['event_id', 'is_active']);
            $table->index(['category_id', 'subcategory_id']);
            $table->index('early_bird_end_date');
            });
        }

        // Ticket Type Day Access - Ticket type → allowed days mapping
        if (!Schema::hasTable('ticket_type_day_access')) {
            Schema::create('ticket_type_day_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_type_id')->constrained('ticket_types')->onDelete('cascade');
            $table->foreignId('event_day_id')->constrained('event_days')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['ticket_type_id', 'event_day_id']);
            });
        }

        // Ticket Inventory - Atomic stock control
        if (!Schema::hasTable('ticket_inventory')) {
            Schema::create('ticket_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_type_id')->unique()->constrained('ticket_types')->onDelete('cascade');
            $table->integer('reserved_qty')->default(0);
            $table->integer('sold_qty')->default(0);
            $table->timestamps();
            });
        }

        // Ticket Category Ticket Rules - Allowed combinations validation
        if (!Schema::hasTable('ticket_category_ticket_rules')) {
            Schema::create('ticket_category_ticket_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_category_id')->constrained('ticket_registration_categories')->onDelete('cascade');
            $table->foreignId('ticket_type_id')->constrained('ticket_types')->onDelete('cascade');
            $table->foreignId('subcategory_id')->nullable()->constrained('ticket_subcategories')->onDelete('cascade');
            $table->json('allowed_days_json')->nullable(); // Array of event_day_ids
            $table->timestamps();

            // Use shorter custom index name to avoid MySQL 64 character limit
            $table->index(['registration_category_id', 'ticket_type_id'], 'ticket_rules_reg_cat_ticket_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_category_ticket_rules');
        Schema::dropIfExists('ticket_inventory');
        Schema::dropIfExists('ticket_type_day_access');
        Schema::dropIfExists('ticket_types');
        Schema::dropIfExists('ticket_subcategories');
        Schema::dropIfExists('ticket_categories');
        Schema::dropIfExists('ticket_registration_categories');
        Schema::dropIfExists('event_days');
        Schema::dropIfExists('ticket_events_config');
    }
};

