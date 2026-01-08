<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Ticket\TicketType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ticket_types', function (Blueprint $table) {
            if (!Schema::hasColumn('ticket_types', 'slug')) {
                $table->string('slug', 255)->nullable()->after('name');
                $table->unique(['event_id', 'slug'], 'ticket_types_event_slug_unique');
            }
        });
        
        // Generate slugs for existing ticket types
        TicketType::whereNull('slug')->chunk(100, function ($ticketTypes) {
            foreach ($ticketTypes as $ticketType) {
                $baseSlug = Str::slug($ticketType->name);
                $slug = $baseSlug;
                $counter = 1;
                
                // Ensure uniqueness within the event
                while (TicketType::where('event_id', $ticketType->event_id)
                    ->where('slug', $slug)
                    ->where('id', '!=', $ticketType->id)
                    ->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                
                $ticketType->slug = $slug;
                $ticketType->save();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_types', function (Blueprint $table) {
            if (Schema::hasColumn('ticket_types', 'slug')) {
                $table->dropUnique('ticket_types_event_slug_unique');
                $table->dropColumn('slug');
            }
        });
    }
};
