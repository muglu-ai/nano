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
        if (Schema::hasTable('ticket_promo_codes')) {
            Schema::table('ticket_promo_codes', function (Blueprint $table) {
                // Organization binding for tracking
                $table->string('organization_name')->nullable()->after('code');
                
                // Category and day restrictions
                $table->json('applicable_registration_category_ids_json')->nullable()->after('applicable_ticket_ids_json');
                $table->json('applicable_ticket_category_ids_json')->nullable()->after('applicable_registration_category_ids_json');
                $table->json('applicable_event_day_ids_json')->nullable()->after('applicable_ticket_category_ids_json');
                
                // Delegate limits
                $table->integer('max_delegates')->nullable()->after('max_uses_per_contact');
                $table->integer('min_delegates')->default(1)->after('max_delegates');
                
                // Additional fields
                $table->boolean('apply_to_base_amount_only')->default(true)->after('min_delegates');
                $table->text('description')->nullable()->after('apply_to_base_amount_only');
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')->after('description');
                
                // Indexes for better query performance
                $table->index('organization_name');
                $table->index('created_by');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ticket_promo_codes')) {
            Schema::table('ticket_promo_codes', function (Blueprint $table) {
                $table->dropIndex(['organization_name']);
                $table->dropIndex(['created_by']);
                
                $table->dropForeign(['created_by']);
                $table->dropColumn([
                    'organization_name',
                    'applicable_registration_category_ids_json',
                    'applicable_ticket_category_ids_json',
                    'applicable_event_day_ids_json',
                    'max_delegates',
                    'min_delegates',
                    'apply_to_base_amount_only',
                    'description',
                    'created_by',
                ]);
            });
        }
    }
};
