<?php

namespace App\Services;

use App\Models\Ticket\Ticket;
use App\Models\Ticket\TicketOrder;
use App\Models\Ticket\TicketDelegate;
use App\Models\Ticket\TicketDelegateAssignment;
use App\Models\Ticket\TicketType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketIssuanceService
{
    /**
     * Issue tickets for a paid order
     * Creates TicketDelegateAssignment and Ticket records for all delegates
     */
    public function issueTicketsForOrder(TicketOrder $order): bool
    {
        try {
            DB::beginTransaction();

            // Reload order with relationships
            $order = TicketOrder::with(['registration.delegates', 'items.ticketType'])->find($order->id);

            // Check if order is paid
            if ($order->status !== 'paid') {
                Log::warning('TicketIssuanceService: Attempted to issue tickets for unpaid order', [
                    'order_id' => $order->id,
                    'order_no' => $order->order_no,
                    'status' => $order->status,
                ]);
                DB::rollBack();
                return false;
            }

            // Get registration and delegates
            $registration = $order->registration;
            if (!$registration) {
                Log::error('TicketIssuanceService: Registration not found for order', [
                    'order_id' => $order->id,
                ]);
                DB::rollBack();
                return false;
            }

            $delegates = $registration->delegates;
            if ($delegates->isEmpty()) {
                Log::warning('TicketIssuanceService: No delegates found for registration', [
                    'registration_id' => $registration->id,
                    'order_id' => $order->id,
                ]);
                DB::rollBack();
                return false;
            }

            // Get order items (ticket types and quantities)
            $orderItems = $order->items;
            if ($orderItems->isEmpty()) {
                Log::error('TicketIssuanceService: No order items found', [
                    'order_id' => $order->id,
                ]);
                DB::rollBack();
                return false;
            }

            // Determine nationality for pricing
            $nationality = $registration->nationality ?? 'national';
            $isInternational = ($nationality === 'International' || $nationality === 'international');
            $nationalityForPrice = $isInternational ? 'international' : 'national';

            // Process each order item
            $delegateIndex = 0;
            foreach ($orderItems as $orderItem) {
                $ticketType = $orderItem->ticketType;
                if (!$ticketType) {
                    Log::warning('TicketIssuanceService: Ticket type not found for order item', [
                        'order_item_id' => $orderItem->id,
                        'ticket_type_id' => $orderItem->ticket_type_id,
                    ]);
                    continue;
                }

                $quantity = $orderItem->quantity;
                
                // Get day access information
                $dayAccessSnapshot = $this->getDayAccessSnapshot($ticketType, $orderItem->selected_event_day_id);
                
                // Get price snapshot
                $priceSnapshot = $orderItem->unit_price;
                $pricingTypeSnapshot = $orderItem->pricing_type ?? 'regular';

                // Issue tickets for the quantity specified
                for ($i = 0; $i < $quantity && $delegateIndex < $delegates->count(); $i++) {
                    $delegate = $delegates[$delegateIndex];
                    
                    // Check if ticket already exists for this delegate
                    $existingTicket = Ticket::where('delegate_id', $delegate->id)->first();
                    if ($existingTicket) {
                        Log::info('TicketIssuanceService: Ticket already exists for delegate', [
                            'delegate_id' => $delegate->id,
                            'ticket_id' => $existingTicket->id,
                        ]);
                        $delegateIndex++;
                        continue;
                    }

                    // Create or update TicketDelegateAssignment (use registration's subcategory if set, else ticket type's)
                    $subcategoryId = $registration->subcategory_id ?? $ticketType->subcategory_id ?? null;
                    $assignment = TicketDelegateAssignment::updateOrCreate(
                        ['delegate_id' => $delegate->id],
                        [
                            'ticket_type_id' => $ticketType->id,
                            'subcategory_id' => $subcategoryId,
                            'day_access_snapshot_json' => $dayAccessSnapshot,
                            'price_snapshot' => $priceSnapshot,
                            'pricing_type_snapshot' => $pricingTypeSnapshot,
                        ]
                    );

                    // Create Ticket record
                    $ticket = Ticket::create([
                        'event_id' => $registration->event_id,
                        'delegate_id' => $delegate->id,
                        'ticket_type_id' => $ticketType->id,
                        'status' => 'issued',
                        'access_snapshot_json' => $dayAccessSnapshot,
                        'source_type' => 'regular',
                        'source_id' => $order->id,
                    ]);

                    Log::info('TicketIssuanceService: Ticket issued', [
                        'ticket_id' => $ticket->id,
                        'delegate_id' => $delegate->id,
                        'ticket_type_id' => $ticketType->id,
                        'order_id' => $order->id,
                    ]);

                    $delegateIndex++;
                }
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('TicketIssuanceService: Failed to issue tickets', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Get day access snapshot for a ticket type
     */
    private function getDayAccessSnapshot(TicketType $ticketType, $selectedDayId = null): ?array
    {
        // If ticket type has all days access, return null (means all days)
        if ($ticketType->all_days_access) {
            return null;
        }

        // If a specific day was selected, return that day
        if ($selectedDayId) {
            return [$selectedDayId];
        }

        // Otherwise, get all event days for this ticket type
        // This is a simplified version - you may need to adjust based on your event days structure
        return null; // null means all days
    }
}
