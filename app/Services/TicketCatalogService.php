<?php

namespace App\Services;

use App\Models\Events;
use App\Models\Ticket\TicketEventConfig;
use App\Models\Ticket\TicketType;
use App\Models\Ticket\TicketInventory;
use App\Models\Ticket\EventDay;
use Illuminate\Support\Facades\Cache;

class TicketCatalogService
{
    /**
     * Get event configuration
     */
    public function getEventConfig($eventId)
    {
        return Cache::remember("ticket_event_config_{$eventId}", 3600, function () use ($eventId) {
            return TicketEventConfig::where('event_id', $eventId)->first();
        });
    }

    /**
     * Validate ticket availability
     */
    public function validateTicketAvailability($ticketTypeId, $quantity = 1)
    {
        $ticketType = TicketType::findOrFail($ticketTypeId);

        // Check if ticket is active
        if (!$ticketType->is_active) {
            return [
                'available' => false,
                'message' => 'This ticket type is not currently available.',
            ];
        }

        // Check if ticket is on sale
        if (!$ticketType->isOnSale()) {
            return [
                'available' => false,
                'message' => 'This ticket type is not currently on sale.',
            ];
        }

        // Check capacity
        if ($ticketType->capacity !== null) {
            $available = $ticketType->getAvailableQuantity();
            if ($available < $quantity) {
                return [
                    'available' => false,
                    'message' => "Only {$available} tickets available.",
                    'available_qty' => $available,
                ];
            }
        }

        return [
            'available' => true,
            'message' => 'Ticket is available.',
        ];
    }

    /**
     * Get current price for a ticket type
     */
    public function getCurrentPrice($ticketTypeId)
    {
        $ticketType = TicketType::findOrFail($ticketTypeId);
        return $ticketType->getCurrentPrice();
    }

    /**
     * Check if event setup is complete
     */
    public function isEventSetupComplete($eventId)
    {
        $config = $this->getEventConfig($eventId);
        
        if (!$config) {
            return false;
        }

        return $config->isSetupComplete();
    }

    /**
     * Get ticket types for an event
     */
    public function getTicketTypesForEvent($eventId, $includeInactive = false)
    {
        $query = TicketType::where('event_id', $eventId)
            ->with(['category', 'subcategory', 'inventory', 'eventDays']);

        if (!$includeInactive) {
            $query->where('is_active', true);
        }

        return $query->orderBy('sort_order')->get();
    }

    /**
     * Get available ticket types for public display
     */
    public function getAvailableTicketTypes($eventId)
    {
        return Cache::remember("available_ticket_types_{$eventId}", 300, function () use ($eventId) {
            $ticketTypes = $this->getTicketTypesForEvent($eventId);
            
            return $ticketTypes->filter(function ($ticketType) {
                $availability = $this->validateTicketAvailability($ticketType->id);
                return $availability['available'];
            })->values();
        });
    }

    /**
     * Clear cache for event
     */
    public function clearEventCache($eventId)
    {
        Cache::forget("ticket_event_config_{$eventId}");
        Cache::forget("available_ticket_types_{$eventId}");
    }
}

