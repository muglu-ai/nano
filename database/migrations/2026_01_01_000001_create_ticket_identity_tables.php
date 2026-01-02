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
        // Ticket Contacts - High-volume attendee identity
        if (!Schema::hasTable('ticket_contacts')) {
            Schema::create('ticket_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->index();
            $table->string('phone')->nullable()->index();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->timestamps();

            // Indexes for faster lookups
            $table->index(['email', 'phone']);
            });
        }

        // Ticket Accounts - Optional login layer
        if (!Schema::hasTable('ticket_accounts')) {
            Schema::create('ticket_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->unique()->constrained('ticket_contacts')->onDelete('cascade');
            $table->enum('status', ['active', 'suspended', 'inactive'])->default('active');
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
            });
        }

        // Ticket OTP Requests - Enhanced OTP with throttling
        if (!Schema::hasTable('ticket_otp_requests')) {
            Schema::create('ticket_otp_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained('ticket_contacts')->onDelete('cascade');
            $table->enum('channel', ['email', 'sms'])->default('email');
            $table->string('otp_hash');
            $table->timestamp('expires_at');
            $table->integer('attempts')->default(0);
            $table->enum('status', ['pending', 'verified', 'expired'])->default('pending');
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index(['contact_id', 'status']);
            $table->index(['ip_address', 'created_at']);
            });
        }

        // Ticket Magic Links - Guest access tokens
        if (!Schema::hasTable('ticket_magic_links')) {
            Schema::create('ticket_magic_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained('ticket_contacts')->onDelete('cascade');
            $table->string('token')->unique();
            $table->enum('purpose', ['manage-booking', 'continue-registration'])->default('manage-booking');
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->index(['token', 'expires_at']);
            $table->index(['contact_id', 'purpose']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_magic_links');
        Schema::dropIfExists('ticket_otp_requests');
        Schema::dropIfExists('ticket_accounts');
        Schema::dropIfExists('ticket_contacts');
    }
};

