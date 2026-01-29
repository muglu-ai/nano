<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Modify the enum to include 'super-admin'
        // Note: MySQL doesn't support ALTER ENUM directly, so we need to use raw SQL
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('exhibitor', 'co-exhibitor', 'sponsor', 'admin', 'super-admin') NOT NULL");
    }

    public function down(): void
    {
        // Remove super-admin role (but keep existing users)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('exhibitor', 'co-exhibitor', 'sponsor', 'admin') NOT NULL");
    }
};
