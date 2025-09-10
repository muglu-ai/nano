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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('company_name');
            $table->string('address');
            $table->string('postal_code');
            $table->foreignId('city_id')->constrained('cities');
            $table->foreignId('state_id')->constrained('states');
            $table->foreignId('country_id')->constrained('countries');
            $table->string('landline');
            $table->string('company_email');
            $table->string('website');
            $table->enum('main_product_category', ['Category1', 'Category2']); // Replace with actual categories
            $table->foreignId('headquarters_country_id')->constrained('countries');
            $table->foreignId('sector_id')->constrained('sectors');
            $table->string('type_of_business');
            $table->string('comments');
            $table->boolean('participated_previous')->default(false);
            $table->boolean('semi_member')->default(false);
            $table->enum('stall_category', ['Shell Scheme', 'Bare Space']);
            $table->integer('booth_count');
            $table->enum('payment_currency', ['EUR', 'INR']);
            $table->enum('status', ['initiated', 'submitted', 'approved', 'rejected']);
            $table->string('application_id')->unique()->nullable(false);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
