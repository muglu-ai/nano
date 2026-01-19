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
        if (Schema::hasTable('ticket_accounts')) {
            Schema::table('ticket_accounts', function (Blueprint $table) {
                if (!Schema::hasColumn('ticket_accounts', 'password')) {
                    $table->string('password')->nullable()->after('contact_id');
                }
                if (!Schema::hasColumn('ticket_accounts', 'email_verified_at')) {
                    $table->timestamp('email_verified_at')->nullable()->after('password');
                }
                if (!Schema::hasColumn('ticket_accounts', 'remember_token')) {
                    $table->string('remember_token', 100)->nullable()->after('email_verified_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ticket_accounts')) {
            Schema::table('ticket_accounts', function (Blueprint $table) {
                if (Schema::hasColumn('ticket_accounts', 'password')) {
                    $table->dropColumn('password');
                }
                if (Schema::hasColumn('ticket_accounts', 'email_verified_at')) {
                    $table->dropColumn('email_verified_at');
                }
                if (Schema::hasColumn('ticket_accounts', 'remember_token')) {
                    $table->dropColumn('remember_token');
                }
            });
        }
    }
};
