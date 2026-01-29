<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_field_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('form_type', 50)->default('startup-zone');
            $table->string('version', 20)->default('1.0'); // Version number (e.g., '1.0', '1.1', '2.0')
            $table->string('field_name', 100); // e.g., 'company_name', 'gst_no'
            $table->string('field_label', 255); // Display label
            $table->boolean('is_required')->default(true);
            $table->text('validation_rules')->nullable(); // JSON: additional validation rules
            $table->integer('field_order')->default(0); // Display order
            $table->string('field_group', 50)->nullable(); // e.g., 'company_info', 'contact_info'
            $table->boolean('is_active')->default(true);
            $table->boolean('is_current_version')->default(false); // Only one version can be current
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->unique(['form_type', 'field_name', 'version'], 'unique_form_field_version');
            $table->index('form_type');
            $table->index('version');
            $table->index('is_active');
            $table->index('is_current_version');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_field_configurations');
    }
};
