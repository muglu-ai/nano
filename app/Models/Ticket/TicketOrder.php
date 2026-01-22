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
        'secure_token',
        'subtotal', // Sum of all item subtotals
        'gst_total', // Total GST across all items
        'cgst_rate', // CGST rate percentage
        'cgst_total', // Total CGST amount
        'sgst_rate', // SGST rate percentage
        'sgst_total', // Total SGST amount
        'igst_rate', // IGST rate percentage
        'igst_total', // Total IGST amount
        'gst_type', // 'cgst_sgst' or 'igst'
        'processing_charge_total', // Total processing charges across all items
        'discount_amount', // Promo code discount
        'promo_code_id',
        'total', // Final total: subtotal + gst_total + processing_charge_total - discount_amount
        'status', // 'pending', 'paid', 'cancelled', 'refunded'
        'payment_status', // 'pending', 'paid', 'complimentary', 'cancelled', 'refunded'
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (empty($order->secure_token)) {
                $order->secure_token = bin2hex(random_bytes(32));
            }
        });
    }

    protected $casts = [
        'subtotal' => 'decimal:2',
        'gst_total' => 'decimal:2',
        'cgst_rate' => 'decimal:2',
        'cgst_total' => 'decimal:2',
        'sgst_rate' => 'decimal:2',
        'sgst_total' => 'decimal:2',
        'igst_rate' => 'decimal:2',
        'igst_total' => 'decimal:2',
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

    /**
     * Get upgrade request for this order (if it's an upgrade order)
     */
    public function upgradeRequest(): HasOne
    {
        return $this->hasOne(TicketUpgradeRequest::class, 'upgrade_order_id');
    }

    /**
     * Check if order is complimentary (100% discount)
     */
    public function isComplimentary(): bool
    {
        return $this->payment_status === 'complimentary' || 
               ($this->total <= 0 && $this->discount_amount > 0);
    }
}

