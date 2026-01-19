<?php

namespace App\Services;

use App\Models\Ticket\TicketUpgradeRequest;
use App\Models\Ticket\TicketUpgrade;
use App\Models\Ticket\TicketOrder;
use App\Models\Ticket\TicketOrderItem;
use App\Models\Ticket\Ticket;
use App\Models\Ticket\TicketDelegate;
use App\Models\Ticket\TicketDelegateAssignment;
use App\Models\Ticket\TicketType;
use App\Models\Ticket\TicketReceipt;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TicketUpgradeService
{
    /**
     * Create upgrade request
     */
    public function createUpgradeRequest(string $type, array $data): TicketUpgradeRequest
    {
        $expiresAt = now()->addHours(24); // 24 hours expiry

        return TicketUpgradeRequest::create([
            'request_type' => $type,
            'contact_id' => $data['contact_id'],
            'registration_id' => $data['registration_id'] ?? null,
            'upgrade_data_json' => $data['upgrade_data'],
            'price_difference' => $data['price_difference'],
            'gst_amount' => $data['gst_amount'],
            'processing_charge_amount' => $data['processing_charge_amount'],
            'total_amount' => $data['total_amount'],
            'status' => 'pending',
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * Calculate price difference
     * This calculates the remaining amount by subtracting already paid amount from new total
     */
    public function calculatePriceDifference(
        TicketType $oldTicketType,
        TicketType $newTicketType,
        int $quantity,
        string $nationality = 'national',
        ?float $alreadyPaidAmount = null // Price snapshot from assignment (what was actually paid)
    ): array {
        $nationalityForPrice = ($nationality === 'International' || $nationality === 'international') 
            ? 'international' 
            : 'national';

        // Get current prices from tables
        $oldPrice = $alreadyPaidAmount ?? $oldTicketType->getCurrentPrice($nationalityForPrice);
        $newPrice = $newTicketType->getCurrentPrice($nationalityForPrice);

        // Calculate new total with GST and processing charges
        $newSubtotal = $newPrice * $quantity;
        $newTotals = $this->applyGstAndCharges($newSubtotal, $nationality);
        $newTotalAmount = $newTotals['total_amount'];

        // Calculate old total (what was already paid) - use price_snapshot if available
        // If alreadyPaidAmount is provided, it's the unit price, so calculate total
        if ($alreadyPaidAmount !== null) {
            $oldSubtotal = $alreadyPaidAmount * $quantity;
            $oldTotals = $this->applyGstAndCharges($oldSubtotal, $nationality);
            $oldTotalAmount = $oldTotals['total_amount'];
        } else {
            // Fallback: calculate from current old price
            $oldSubtotal = $oldPrice * $quantity;
            $oldTotals = $this->applyGstAndCharges($oldSubtotal, $nationality);
            $oldTotalAmount = $oldTotals['total_amount'];
        }

        // Calculate remaining amount (new total - old total)
        $remainingAmount = $newTotalAmount - $oldTotalAmount;

        // The remaining amount is what needs to be paid
        // No need to apply GST/charges again as it's already included in the difference
        return [
            'old_price' => $oldPrice,
            'old_total' => $oldTotalAmount,
            'new_price' => $newPrice,
            'new_total' => $newTotalAmount,
            'price_difference' => $newPrice - $oldPrice, // Unit price difference
            'remaining_amount' => $remainingAmount, // Total amount to pay (already includes GST/charges)
            'gst_amount' => $newTotals['gst_amount'] - ($oldTotals['gst_amount'] ?? 0), // GST on difference
            'processing_charge_amount' => $newTotals['processing_charge_amount'] - ($oldTotals['processing_charge_amount'] ?? 0), // Processing charge on difference
            'total_amount' => $remainingAmount, // This is what needs to be paid
        ];
    }

    /**
     * Apply GST and processing charges
     */
    public function applyGstAndCharges(float $subtotal, string $nationality = 'national'): array
    {
        $isInternational = ($nationality === 'International' || $nationality === 'international');
        
        $gstRate = config('constants.GST_RATE', 18);
        $processingChargeRate = $isInternational 
            ? config('constants.INT_PROCESSING_CHARGE', 9)
            : config('constants.IND_PROCESSING_CHARGE', 3);

        $gstAmount = round(($subtotal * $gstRate) / 100);
        $processingChargeAmount = round((($subtotal + $gstAmount) * $processingChargeRate) / 100);
        $total = round($subtotal + $gstAmount + $processingChargeAmount);

        return [
            'gst_amount' => $gstAmount,
            'processing_charge_amount' => $processingChargeAmount,
            'total_amount' => $total,
        ];
    }

    /**
     * Create upgrade order from request
     */
    public function createUpgradeOrder(int $requestId): TicketOrder
    {
        $request = TicketUpgradeRequest::findOrFail($requestId);

        if ($request->status !== 'pending') {
            throw new \Exception('Upgrade request is not pending.');
        }

        // Generate unique order number
        $orderNo = $this->generateUniqueOrderNumber();

        // Get registration (required for order)
        $registration = $request->registration;
        if (!$registration) {
            // For individual upgrades, get registration from first ticket
            $upgradeData = $request->upgrade_data_json;
            $firstTicket = Ticket::find($upgradeData['tickets'][0]['ticket_id'] ?? null);
            if ($firstTicket && $firstTicket->delegate) {
                $registration = $firstTicket->delegate->registration;
            }
        }

        if (!$registration) {
            throw new \Exception('Unable to find registration for upgrade order.');
        }

        // Create order
        $order = TicketOrder::create([
            'registration_id' => $registration->id,
            'order_no' => $orderNo,
            'subtotal' => $request->price_difference,
            'gst_total' => $request->gst_amount,
            'processing_charge_total' => $request->processing_charge_amount,
            'discount_amount' => 0,
            'total' => $request->total_amount,
            'status' => 'pending',
        ]);

        // Create order item
        $upgradeData = $request->upgrade_data_json;
        $firstTicketData = $upgradeData['tickets'][0] ?? null;
        
        if ($firstTicketData) {
            $newTicketType = TicketType::find($firstTicketData['new_ticket_type_id']);
            if ($newTicketType) {
                TicketOrderItem::create([
                    'order_id' => $order->id,
                    'ticket_type_id' => $newTicketType->id,
                    'selected_event_day_id' => null,
                    'quantity' => count($upgradeData['tickets']),
                    'unit_price' => $request->price_difference / count($upgradeData['tickets']),
                    'subtotal' => $request->price_difference,
                    'gst_rate' => config('constants.GST_RATE', 18),
                    'gst_amount' => $request->gst_amount,
                    'processing_charge_rate' => (($request->contact->registrations()->first()->nationality ?? 'Indian') === 'International') 
                        ? config('constants.INT_PROCESSING_CHARGE', 9)
                        : config('constants.IND_PROCESSING_CHARGE', 3),
                    'processing_charge_amount' => $request->processing_charge_amount,
                    'total' => $request->total_amount,
                    'pricing_type' => $newTicketType->isEarlyBirdActive() ? 'early_bird' : 'regular',
                ]);
            }
        }

        // Link order to request
        $request->update(['upgrade_order_id' => $order->id]);

        return $order;
    }

    /**
     * Process payment success - update master tables
     */
    public function processPaymentSuccess(int $requestId, array $paymentData = []): void
    {
        DB::beginTransaction();
        try {
            $request = TicketUpgradeRequest::findOrFail($requestId);

            if ($request->status === 'paid') {
                Log::warning('Upgrade request already processed', ['request_id' => $requestId]);
                DB::rollBack();
                return;
            }

            // Mark request as paid
            $request->markAsPaid();

            // Update tickets and related tables
            $this->updateTicketsAfterPayment($requestId);

            // Generate final receipt
            $this->generateUpgradeReceipt($requestId, 'final');

            DB::commit();

            Log::info('Upgrade payment processed successfully', [
                'request_id' => $requestId,
                'payment_data' => $paymentData,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process upgrade payment', [
                'request_id' => $requestId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Update tickets after payment
     */
    public function updateTicketsAfterPayment(int $requestId): void
    {
        $request = TicketUpgradeRequest::findOrFail($requestId);
        $upgradeData = $request->upgrade_data_json;

        foreach ($upgradeData['tickets'] as $ticketData) {
            $ticket = Ticket::find($ticketData['ticket_id']);
            if (!$ticket) {
                continue;
            }

            $newTicketType = TicketType::find($ticketData['new_ticket_type_id']);
            if (!$newTicketType) {
                continue;
            }

            // Update ticket
            $ticket->update([
                'ticket_type_id' => $newTicketType->id,
                'status' => 'issued', // Ensure ticket is issued
            ]);

            // Update delegate assignment
            $assignment = TicketDelegateAssignment::where('delegate_id', $ticket->delegate_id)->first();
            if ($assignment) {
                $assignment->update([
                    'ticket_type_id' => $newTicketType->id,
                    'price_snapshot' => $ticketData['new_price'],
                    'pricing_type_snapshot' => $newTicketType->isEarlyBirdActive() ? 'early_bird' : 'regular',
                ]);
            }

            // Create upgrade record
            TicketUpgrade::create([
                'upgrade_request_id' => $requestId,
                'contact_id' => $request->contact_id,
                'old_ticket_id' => $ticket->id,
                'new_ticket_id' => $ticket->id, // Same ticket, just upgraded
                'upgrade_order_id' => $request->upgrade_order_id,
            ]);
        }
    }

    /**
     * Generate upgrade receipt
     */
    public function generateUpgradeReceipt(int $requestId, string $type = 'provisional'): TicketReceipt
    {
        $request = TicketUpgradeRequest::findOrFail($requestId);
        $order = $request->upgradeOrder;

        if (!$order) {
            throw new \Exception('Order not found for upgrade request.');
        }

        $receiptNo = $this->generateReceiptNumber($type);

        $receipt = TicketReceipt::updateOrCreate(
            [
                'registration_id' => $order->registration_id,
                'order_id' => $order->id,
                'type' => $type === 'final' ? 'upgrade_final' : 'upgrade_provisional',
            ],
            [
                'receipt_no' => $receiptNo,
                'issued_at' => now(),
            ]
        );

        return $receipt;
    }

    /**
     * Cancel upgrade request
     */
    public function cancelUpgradeRequest(int $requestId): void
    {
        $request = TicketUpgradeRequest::findOrFail($requestId);

        if ($request->status === 'paid') {
            throw new \Exception('Cannot cancel a paid upgrade request.');
        }

        $request->markAsCancelled();
    }

    /**
     * Generate unique order number
     */
    private function generateUniqueOrderNumber(): string
    {
        $prefix = 'TKT-UPG-';
        $year = date('Y');
        $maxAttempts = 100;

        for ($i = 0; $i < $maxAttempts; $i++) {
            $sequence = str_pad((string) rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            $orderNo = $prefix . $year . '-' . $sequence;

            if (!TicketOrder::where('order_no', $orderNo)->exists()) {
                return $orderNo;
            }
        }

        throw new \Exception('Unable to generate unique order number.');
    }

    /**
     * Generate receipt number
     */
    private function generateReceiptNumber(string $type): string
    {
        $prefix = $type === 'final' ? 'RCP-UPG-F-' : 'RCP-UPG-P-';
        $year = date('Y');
        $sequence = str_pad((string) rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        return $prefix . $year . '-' . $sequence;
    }
}
