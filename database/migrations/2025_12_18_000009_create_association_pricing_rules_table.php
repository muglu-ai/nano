<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('association_pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->string('association_name', 255)->unique();
            $table->string('display_name', 255);
            $table->string('logo_path', 255)->nullable(); // Path to association logo image
            $table->string('promocode', 100)->nullable()->unique(); // Unique promocode for this association
            $table->decimal('base_price', 10, 2)->default(52000.00);
            $table->decimal('special_price', 10, 2)->nullable();
            $table->boolean('is_complimentary')->default(false);
            $table->integer('max_registrations')->nullable();
            $table->integer('current_registrations')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->text('entitlements')->nullable(); // JSON or text for entitlements
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('association_name');
            $table->index('promocode');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('association_pricing_rules');
    }
};
