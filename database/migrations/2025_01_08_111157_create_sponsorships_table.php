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
        Schema::create('sponsorships', function (Blueprint $table) {
            $table->id();
            $table->string('sponsorship_id')->unique()->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('sponsorship_item');
            $table->decimal('price', 10, 2);
            $table->enum('status', ['initiated', 'submitted', 'pending', 'approved', 'rejected']);
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->onDelete('cascade');
            $table->foreignId('sponsorship_item_id')->constrained('sponsor_items')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('sponsorship_item_count');
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamp('submitted_date')->nullable();
            $table->timestamp('approval_date')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('application_id');
            $table->index('invoice_id');
            $table->index('sponsorship_item_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sponsorships');
    }
};
