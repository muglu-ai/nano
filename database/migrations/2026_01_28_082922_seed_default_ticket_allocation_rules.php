<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\TicketAllocationRule;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * NOTE: This seeds default allocation rules based on hardcoded logic.
     * Admin needs to configure which ticket_type_ids represent:
     * - Exhibitor passes (stall manning)
     * - Complimentary delegates
     * 
     * The ticket_allocations JSON will need to be updated with actual ticket_type_ids
     * after ticket types are created in the system.
     */
    public function up(): void
    {
        // Get default event (first event or event_id = 1)
        $defaultEvent = DB::table('events')->orderBy('id')->first();
        $defaultEventId = $defaultEvent ? $defaultEvent->id : null;

        // Rules based on hardcoded logic:
        // Format: booth_area_range => [exhibitors, delegates]
        $rules = [
            ['min' => 3, 'max' => 8, 'exhibitors' => 2, 'delegates' => 1],
            ['min' => 9, 'max' => 11, 'exhibitors' => 2, 'delegates' => 1],
            ['min' => 12, 'max' => 14, 'exhibitors' => 3, 'delegates' => 2],
            ['min' => 15, 'max' => 17, 'exhibitors' => 4, 'delegates' => 2],
            ['min' => 18, 'max' => 26, 'exhibitors' => 4, 'delegates' => 2],
            ['min' => 27, 'max' => 29, 'exhibitors' => 6, 'delegates' => 3],
            ['min' => 30, 'max' => 36, 'exhibitors' => 7, 'delegates' => 4],
            ['min' => 37, 'max' => 53, 'exhibitors' => 7, 'delegates' => 5],
            ['min' => 54, 'max' => 71, 'exhibitors' => 7, 'delegates' => 7],
            ['min' => 72, 'max' => 81, 'exhibitors' => 7, 'delegates' => 7],
            ['min' => 82, 'max' => 135, 'exhibitors' => 7, 'delegates' => 7],
        ];

        // Try to find ticket types for exhibitor passes and delegates
        // Look for ticket types with names/slugs containing "exhibitor", "stall", "delegate", "complimentary"
        $exhibitorTicketType = DB::table('ticket_types')
            ->where(function($q) {
                $q->where('name', 'like', '%exhibitor%')
                  ->orWhere('name', 'like', '%stall%')
                  ->orWhere('slug', 'like', '%exhibitor%')
                  ->orWhere('slug', 'like', '%stall%');
            })
            ->where('is_active', true)
            ->first();

        $delegateTicketType = DB::table('ticket_types')
            ->where(function($q) {
                $q->where('name', 'like', '%delegate%')
                  ->orWhere('name', 'like', '%complimentary%')
                  ->orWhere('slug', 'like', '%delegate%')
                  ->orWhere('slug', 'like', '%complimentary%');
            })
            ->where('is_active', true)
            ->first();

        // If ticket types not found, create placeholder rules that admin needs to configure
        // For now, we'll create rules with empty allocations - admin must configure ticket_type_ids
        foreach ($rules as $index => $rule) {
            $ticketAllocations = [];

            // If we found ticket types, use them
            if ($exhibitorTicketType && $rule['exhibitors'] > 0) {
                $ticketAllocations[$exhibitorTicketType->id] = $rule['exhibitors'];
            }

            if ($delegateTicketType && $rule['delegates'] > 0) {
                $ticketAllocations[$delegateTicketType->id] = $rule['delegates'];
            }

            // If no ticket types found, create rule with empty allocations
            // Admin will need to configure these manually
            // We'll still create the rule structure so admin knows what needs to be configured
            if (empty($ticketAllocations)) {
                // Create a note in the allocation (using a special key)
                // Admin will need to replace this with actual ticket_type_ids
                $ticketAllocations = [
                    // Placeholder: Admin needs to configure actual ticket_type_ids
                    // Expected: exhibitor_ticket_type_id => exhibitors count
                    // Expected: delegate_ticket_type_id => delegates count
                ];
            }

            TicketAllocationRule::create([
                'event_id' => $defaultEventId,
                'application_type' => null, // Applies to all application types
                'booth_area_min' => $rule['min'],
                'booth_area_max' => $rule['max'],
                'ticket_allocations' => $ticketAllocations,
                'sort_order' => $index,
                'is_active' => !empty($ticketAllocations), // Only active if ticket types are configured
            ]);
        }

        // Also create rules specifically for startup-zone (same structure for now)
        // Admin can modify these separately if needed
        foreach ($rules as $index => $rule) {
            $ticketAllocations = [];

            if ($exhibitorTicketType && $rule['exhibitors'] > 0) {
                $ticketAllocations[$exhibitorTicketType->id] = $rule['exhibitors'];
            }

            if ($delegateTicketType && $rule['delegates'] > 0) {
                $ticketAllocations[$delegateTicketType->id] = $rule['delegates'];
            }

            TicketAllocationRule::create([
                'event_id' => $defaultEventId,
                'application_type' => 'startup-zone',
                'booth_area_min' => $rule['min'],
                'booth_area_max' => $rule['max'],
                'ticket_allocations' => $ticketAllocations,
                'sort_order' => $index + 100, // Offset for startup-zone rules
                'is_active' => !empty($ticketAllocations),
            ]);
        }

        // Create rules for exhibitor-registration (same structure)
        foreach ($rules as $index => $rule) {
            $ticketAllocations = [];

            if ($exhibitorTicketType && $rule['exhibitors'] > 0) {
                $ticketAllocations[$exhibitorTicketType->id] = $rule['exhibitors'];
            }

            if ($delegateTicketType && $rule['delegates'] > 0) {
                $ticketAllocations[$delegateTicketType->id] = $rule['delegates'];
            }

            TicketAllocationRule::create([
                'event_id' => $defaultEventId,
                'application_type' => 'exhibitor-registration',
                'booth_area_min' => $rule['min'],
                'booth_area_max' => $rule['max'],
                'ticket_allocations' => $ticketAllocations,
                'sort_order' => $index + 200, // Offset for exhibitor-registration rules
                'is_active' => !empty($ticketAllocations),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete all seeded rules (optional - you may want to keep them)
        // TicketAllocationRule::truncate();
    }
};
