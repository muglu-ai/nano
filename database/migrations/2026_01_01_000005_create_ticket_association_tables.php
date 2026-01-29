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
        // Ticket Associations - Association/sponsor profiles
        if (!Schema::hasTable('ticket_associations')) {
            Schema::create('ticket_associations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            });
        }

        // Ticket Association Admins - Map portal users to associations
        if (!Schema::hasTable('ticket_association_admins')) {
            Schema::create('ticket_association_admins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('association_id')->constrained('ticket_associations')->onDelete('cascade');
            $table->foreignId('portal_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['association_id', 'portal_user_id']);
            });
        }

        // Ticket Association Allocations - Quota allocations
        if (!Schema::hasTable('ticket_association_allocations')) {
            Schema::create('ticket_association_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('association_id')->constrained('ticket_associations')->onDelete('cascade');
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('ticket_type_id')->constrained('ticket_types')->onDelete('cascade');
            $table->integer('allocated_qty');
            $table->integer('used_qty')->default(0);
            $table->timestamps();

            // Use shorter custom unique name to avoid MySQL 64 character limit
            $table->unique(['association_id', 'event_id', 'ticket_type_id'], 'ticket_assoc_alloc_unique');
            $table->index(['association_id', 'event_id']);
            });
        }

        // Ticket Association Links - Shareable association links
        if (!Schema::hasTable('ticket_association_links')) {
            Schema::create('ticket_association_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('allocation_id')->constrained('ticket_association_allocations')->onDelete('cascade');
            $table->string('token')->unique();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['token', 'expires_at']);
            });
        }

        // Ticket Promo Codes - Admin promo rules
        if (!Schema::hasTable('ticket_promo_codes')) {
            Schema::create('ticket_promo_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('code')->unique();
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('value', 10, 2); // Discount percentage or fixed amount
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_to')->nullable();
            $table->integer('max_uses')->nullable(); // null for unlimited
            $table->integer('max_uses_per_contact')->nullable(); // null for unlimited
            $table->decimal('min_order_amount', 10, 2)->nullable(); // Minimum order amount to apply
            $table->json('applicable_ticket_ids_json')->nullable(); // JSON array of ticket_type_ids (null for all)
            $table->json('rules_json')->nullable(); // Additional rules
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['event_id', 'code']);
            $table->index(['event_id', 'is_active']);
            });
        }

        // Ticket Promo Redemptions - Promo audit
        if (!Schema::hasTable('ticket_promo_redemptions')) {
            Schema::create('ticket_promo_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_id')->constrained('ticket_promo_codes')->onDelete('cascade');
            $table->foreignId('contact_id')->constrained('ticket_contacts')->onDelete('cascade');
            $table->foreignId('order_id')->constrained('ticket_orders')->onDelete('cascade');
            $table->decimal('discount_amount', 10, 2);
            $table->timestamps();

            $table->index(['promo_id', 'contact_id']);
            $table->index('order_id');
            });
        }

        // Ticket Admin Invites - Admin paid invite links
        if (!Schema::hasTable('ticket_admin_invites')) {
            Schema::create('ticket_admin_invites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('created_by_portal_user_id')->constrained('users')->onDelete('cascade');
            $table->string('token')->unique();
            $table->integer('max_uses')->default(1);
            $table->integer('used_count')->default(0);
            $table->json('allowed_ticket_ids_json')->nullable(); // JSON array of ticket_type_ids
            $table->enum('payment_method', ['paid', 'free'])->default('paid');
            $table->decimal('paid_amount', 10, 2)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['token', 'expires_at']);
            });
        }

        // Ticket Bulk Import Jobs - Import job tracking
        if (!Schema::hasTable('ticket_bulk_import_jobs')) {
            Schema::create('ticket_bulk_import_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('uploaded_by_portal_user_id')->constrained('users')->onDelete('cascade');
            $table->string('file_path');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->integer('total_rows')->default(0);
            $table->integer('successful_rows')->default(0);
            $table->integer('failed_rows')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['event_id', 'status']);
            });
        }

        // Ticket Bulk Import Rows - Import row errors
        if (!Schema::hasTable('ticket_bulk_import_rows')) {
            Schema::create('ticket_bulk_import_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('ticket_bulk_import_jobs')->onDelete('cascade');
            $table->integer('row_number');
            $table->json('row_json')->nullable(); // Original row data
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['job_id', 'status']);
            });
        }

        // Ticket Upgrades - Upgrade history
        if (!Schema::hasTable('ticket_upgrades')) {
            Schema::create('ticket_upgrades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained('ticket_contacts')->onDelete('cascade');
            $table->foreignId('old_ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->foreignId('new_ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->foreignId('upgrade_order_id')->nullable()->constrained('ticket_orders')->onDelete('set null');
            $table->timestamps();

            $table->index('contact_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_upgrades');
        Schema::dropIfExists('ticket_bulk_import_rows');
        Schema::dropIfExists('ticket_bulk_import_jobs');
        Schema::dropIfExists('ticket_admin_invites');
        Schema::dropIfExists('ticket_promo_redemptions');
        Schema::dropIfExists('ticket_promo_codes');
        Schema::dropIfExists('ticket_association_links');
        Schema::dropIfExists('ticket_association_allocations');
        Schema::dropIfExists('ticket_association_admins');
        Schema::dropIfExists('ticket_associations');
    }
};

