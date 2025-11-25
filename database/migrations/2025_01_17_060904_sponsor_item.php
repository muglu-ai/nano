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
        // Create sponsor_categories first if not exists
        if (!Schema::hasTable('sponsor_categories')) {
            Schema::create('sponsor_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();
            });
        }

        // Create sponsor_items table
        Schema::create('sponsor_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('category_id')->constrained('sponsor_categories');
            $table->decimal('price', 10, 2);
            $table->decimal('mem_price', 10, 2);
            $table->integer('no_of_items');
            $table->text('quantity_desc')->nullable();
            $table->text('deliverables');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('image_url')->nullable();
            $table->dateTime('deadline')->nullable();
            $table->boolean('is_addon')->default(false);
            $table->timestamps();

            // Indexes
            $table->index('category_id');
            $table->index('no_of_items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
