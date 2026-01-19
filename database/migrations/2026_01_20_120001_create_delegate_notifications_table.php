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
        if (!Schema::hasTable('delegate_notifications')) {
            Schema::create('delegate_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('delegate_id')->nullable()->constrained('ticket_delegates')->onDelete('cascade');
                $table->foreignId('contact_id')->nullable()->constrained('ticket_contacts')->onDelete('cascade');
                $table->string('title');
                $table->text('message');
                $table->enum('type', ['info', 'warning', 'important'])->default('info');
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();

                $table->index(['delegate_id', 'is_read']);
                $table->index(['contact_id', 'is_read']);
                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delegate_notifications');
    }
};
