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
        Schema::create('metroleads_api_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enquiry_id')->nullable();
            $table->json('request_data')->nullable();
            $table->json('response_data')->nullable();
            $table->string('status', 50)->default('pending'); // pending, success, error
            $table->integer('http_code')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->foreign('enquiry_id')->references('id')->on('enquiries')->onDelete('set null');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metroleads_api_logs');
    }
};
