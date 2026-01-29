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
        Schema::create('co_exhibitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->onDelete('cascade');
            $table->string('pavilion_name')->nullable();
            $table->string('co_exhibitor_name');
            $table->string('contact_person');
            $table->string('email');
            $table->string('phone');
            $table->string('relation')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->integer('allocated_passes')->default(0);
            $table->text('proof_document')->nullable();
            $table->string('job_title')->nullable();
            $table->integer('stall_size')->nullable();
            $table->string('booth_number', 25)->nullable();
            $table->string('co_exhibitor_id', 125)->nullable();
            $table->timestamp('approved_At')->nullable();
            $table->boolean('purchase_allowed')->default(false);
            $table->string('address1')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('country')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('application_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('co_exhibitors');
    }
};
