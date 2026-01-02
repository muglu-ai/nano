<?php

namespace App\Models\Ticket;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TicketOrder extends Model
{
    protected $table = 'ticket_orders';

    protected $fillable = [
        'registration_id',
        'order_no',
        'subtotal', // Sum of all item subtotals
        'gst_total', // Total GST across all items
        'processing_charge_total', // Total processing charges across all items
        'discount_amount', // Promo code discount
        'promo_code_id',
        'total', // Final total: subtotal + gst_total + processing_charge_total - discount_amount
        'status', // 'pending', 'paid', 'cancelled', 'refunded'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'gst_total' => 'decimal:2',
        'processing_charge_total' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Get the registration
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(TicketRegistration::class, 'registration_id');
    }

    /**
     * Get order items
     */
    public function items(): HasMany
    {
        return $this->hasMany(TicketOrderItem::class, 'order_id');
    }

    /**
     * Get payments for this order (can have multiple payments)
     * Since TicketPayment now uses order_ids_json, we need to query differently
     */
    public function payments()
    {
        return TicketPayment::whereJsonContains('order_ids_json', $this->id);
    }

    /**
     * Get primary payment (most recent completed payment)
     */
    public function primaryPayment()
    {
        return $this->payments()
            ->where('status', 'completed')
            ->orderBy('paid_at', 'desc')
            ->first();
    }

    /**
     * Get receipt for this order
     */
    public function receipt(): HasOne
    {
        return $this->hasOne(TicketReceipt::class, 'order_id');
    }

    /**
     * Get the promo code used
     */
    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(TicketPromoCode::class, 'promo_code_id');
    }
}

