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
        // Ticket Early Bird Reminders - Track reminder history
        if (!Schema::hasTable('ticket_early_bird_reminders')) {
            Schema::create('ticket_early_bird_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_type_id')->constrained('ticket_types')->onDelete('cascade');
            $table->date('reminder_date');
            $table->foreignId('reminded_to_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reminder_sent_at')->nullable();
            $table->enum('reminder_type', ['email', 'notification', 'both'])->default('email');
            $table->timestamps();

            $table->index(['ticket_type_id', 'reminder_date']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_early_bird_reminders');
    }
};

