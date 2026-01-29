<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Update sectors table to be configurable
        if (!Schema::hasTable('sectors')) {
            Schema::create('sectors', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        } else {
            if (!Schema::hasColumn('sectors', 'is_active')) {
                Schema::table('sectors', function (Blueprint $table) {
                    $table->boolean('is_active')->default(true)->after('name');
                });
            }
            if (!Schema::hasColumn('sectors', 'sort_order')) {
                Schema::table('sectors', function (Blueprint $table) {
                    $table->integer('sort_order')->default(0)->after('is_active');
                });
            }
        }

        // Create sub_sectors table
        if (!Schema::hasTable('sub_sectors')) {
            Schema::create('sub_sectors', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // Create organization_types table
        if (!Schema::hasTable('organization_types')) {
            Schema::create('organization_types', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_sectors');
        Schema::dropIfExists('organization_types');
        // Don't drop sectors as it may have foreign keys
    }
};
