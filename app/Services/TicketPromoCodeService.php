<?php

namespace App\Services;

use App\Models\Ticket\TicketPromoCode;
use App\Models\Ticket\TicketContact;
use App\Models\Ticket\TicketOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketPromoCodeService
{
    /**
     * Validate promocode for given registration data
     * 
     * @param string $code Promocode to validate
     * @param int $eventId Event ID
     * @param array $registrationData Registration data including:
     *   - ticket_type_id
     *   - registration_category_id (optional)
     *   - selected_event_day_id (optional)
     *   - delegate_count
     *   - base_amount (subtotal before GST/charges)
     *   - contact_id (optional, for per-contact limit check)
     * 
     * @return array ['valid' => bool, 'message' => string, 'promoCode' => TicketPromoCode|null, 'discount_amount' => float]
     */
    public function validatePromoCode(string $code, int $eventId, array $registrationData): array
    {
        try {
            // Find promocode
            $promoCode = TicketPromoCode::where('code', strtoupper(trim($code)))
                ->where('event_id', $eventId)
                ->first();

            if (!$promoCode) {
                return [
                    'valid' => false,
                    'message' => 'Invalid promocode. Please check and try again.',
                    'promoCode' => null,
                    'discount_amount' => 0,
                ];
            }

            // Check if active
            if (!$promoCode->is_active) {
                return [
                    'valid' => false,
                    'message' => 'This promocode is currently inactive.',
                    'promoCode' => null,
                    'discount_amount' => 0,
                ];
            }

            // Check validity dates
            $now = now();
            if ($promoCode->valid_from && $now->lt($promoCode->valid_from)) {
                return [
                    'valid' => false,
                    'message' => 'This promocode is not yet valid.',
                    'promoCode' => null,
                    'discount_amount' => 0,
                ];
            }

            if ($promoCode->valid_to && $now->gt($promoCode->valid_to)) {
                return [
                    'valid' => false,
                    'message' => 'This promocode has expired.',
                    'promoCode' => null,
                    'discount_amount' => 0,
                ];
            }

            // Check max uses (global)
            if ($promoCode->max_uses !== null) {
                $usedCount = $promoCode->getUsedCount();
                if ($usedCount >= $promoCode->max_uses) {
                    return [
                        'valid' => false,
                        'message' => 'This promocode has reached its maximum usage limit.',
                        'promoCode' => null,
                        'discount_amount' => 0,
                    ];
                }
            }

            // Check max uses per contact
            if ($promoCode->max_uses_per_contact !== null && isset($registrationData['contact_id'])) {
                $contactUsedCount = DB::table('ticket_promo_redemptions')
                    ->where('promo_id', $promoCode->id)
                    ->where('contact_id', $registrationData['contact_id'])
                    ->count();
                
                if ($contactUsedCount >= $promoCode->max_uses_per_contact) {
                    return [
                        'valid' => false,
                        'message' => 'You have already used this promocode the maximum number of times.',
                        'promoCode' => null,
                        'discount_amount' => 0,
                    ];
                }
            }

            // Check registration category restriction
            if (isset($registrationData['registration_category_id'])) {
                if (!$promoCode->isValidForCategory($registrationData['registration_category_id'])) {
                    return [
                        'valid' => false,
                        'message' => 'This promocode is not valid for your registration category.',
                        'promoCode' => null,
                        'discount_amount' => 0,
                    ];
                }
            }

            // Check ticket category restriction (need to get ticket type first)
            if (isset($registrationData['ticket_type_id'])) {
                $ticketType = \App\Models\Ticket\TicketType::find($registrationData['ticket_type_id']);
                if ($ticketType && $ticketType->category_id) {
                    if (!$promoCode->isValidForTicketCategory($ticketType->category_id)) {
                        return [
                            'valid' => false,
                            'message' => 'This promocode is not valid for the selected ticket category.',
                            'promoCode' => null,
                            'discount_amount' => 0,
                        ];
                    }
                }
            }

            // Check event day restriction
            if (isset($registrationData['selected_event_day_id']) && $registrationData['selected_event_day_id'] !== 'all') {
                if (!$promoCode->isValidForDay($registrationData['selected_event_day_id'])) {
                    return [
                        'valid' => false,
                        'message' => 'This promocode is not valid for the selected event day.',
                        'promoCode' => null,
                        'discount_amount' => 0,
                    ];
                }
            }

            // Check delegate count restrictions
            if (isset($registrationData['delegate_count'])) {
                if (!$promoCode->isValidForDelegateCount($registrationData['delegate_count'])) {
                    $minMsg = $promoCode->min_delegates ? "minimum {$promoCode->min_delegates}" : '';
                    $maxMsg = $promoCode->max_delegates ? "maximum {$promoCode->max_delegates}" : '';
                    $msg = 'This promocode is not valid for the number of delegates.';
                    if ($minMsg || $maxMsg) {
                        $msg .= ' Required: ' . trim("$minMsg $maxMsg");
                    }
                    return [
                        'valid' => false,
                        'message' => $msg,
                        'promoCode' => null,
                        'discount_amount' => 0,
                    ];
                }
            }

            // Check minimum order amount
            $baseAmount = $registrationData['base_amount'] ?? 0;
            if ($promoCode->min_order_amount && $baseAmount < $promoCode->min_order_amount) {
                return [
                    'valid' => false,
                    'message' => "This promocode requires a minimum order amount of " . 
                                number_format($promoCode->min_order_amount, 2) . ".",
                    'promoCode' => null,
                    'discount_amount' => 0,
                ];
            }

            // All validations passed - calculate discount
            $discountAmount = $this->calculateDiscount($promoCode, $baseAmount);

            return [
                'valid' => true,
                'message' => 'Promocode applied successfully!',
                'promoCode' => $promoCode,
                'discount_amount' => $discountAmount,
                'discount_percentage' => $promoCode->type === 'percentage' ? $promoCode->value : null,
            ];
        } catch (\Exception $e) {
            Log::error('Promocode validation error', [
                'code' => $code,
                'event_id' => $eventId,
                'error' => $e->getMessage(),
            ]);

            return [
                'valid' => false,
                'message' => 'An error occurred while validating the promocode. Please try again.',
                'promoCode' => null,
                'discount_amount' => 0,
            ];
        }
    }

    /**
     * Calculate discount on base amount only
     * 
     * @param TicketPromoCode $promoCode
     * @param float $baseAmount Base amount (subtotal before GST/processing charges)
     * @return float Discount amount
     */
    public function calculateDiscount(TicketPromoCode $promoCode, float $baseAmount): float
    {
        return $promoCode->calculateDiscount($baseAmount);
    }

    /**
     * Apply promocode to order
     * 
     * @param TicketOrder $order
     * @param TicketPromoCode $promoCode
     * @return bool Success
     */
    public function applyPromoCode(TicketOrder $order, TicketPromoCode $promoCode): bool
    {
        try {
            DB::beginTransaction();

            // Calculate discount on base amount (subtotal) only
            $discountAmount = $this->calculateDiscount($promoCode, $order->subtotal);

            // Recalculate total with discount
            $newTotal = $order->subtotal + $order->gst_total + $order->processing_charge_total - $discountAmount;

            // Update order
            $order->update([
                'promo_code_id' => $promoCode->id,
                'discount_amount' => $discountAmount,
                'total' => max(0, $newTotal), // Ensure total doesn't go negative
            ]);

            // Create redemption record
            if ($order->registration && $order->registration->contact_id) {
                \App\Models\Ticket\TicketPromoRedemption::create([
                    'promo_id' => $promoCode->id,
                    'contact_id' => $order->registration->contact_id,
                    'order_id' => $order->id,
                    'discount_amount' => $discountAmount,
                ]);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error applying promocode to order', [
                'order_id' => $order->id,
                'promo_code_id' => $promoCode->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Check if promocode results in 100% discount (complimentary)
     * 
     * @param TicketPromoCode $promoCode
     * @param float $baseAmount
     * @return bool
     */
    public function checkComplimentary(TicketPromoCode $promoCode, float $baseAmount): bool
    {
        $discountAmount = $this->calculateDiscount($promoCode, $baseAmount);
        // Consider complimentary if discount covers entire base amount
        // Note: GST and processing charges still apply, so total might not be exactly 0
        return $discountAmount >= $baseAmount;
    }
}
