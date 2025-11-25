<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('requirements_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->nullable()->constrained('applications')->onDelete('cascade');
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('co_exhibitor_id')->nullable();
            $table->string('order_status')->nullable();
            $table->string('delivery_status', 125)->nullable();
            $table->string('remarks', 225)->nullable();
            $table->boolean('delete')->default(false);
            $table->timestamps();

            // Indexes
            $table->index('application_id');
            $table->index('invoice_id');
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('requirements_orders');
    }
};
