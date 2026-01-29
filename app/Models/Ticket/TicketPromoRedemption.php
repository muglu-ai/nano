<?php

namespace App\Models\Ticket;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketPromoRedemption extends Model
{
    protected $table = 'ticket_promo_redemptions';

    protected $fillable = [
        'promo_id',
        'contact_id',
        'order_id',
        'discount_amount',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
    ];

    /**
     * Get the promo code
     */
    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(TicketPromoCode::class, 'promo_id');
    }

    /**
     * Get the contact
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(TicketContact::class, 'contact_id');
    }

    /**
     * Get the order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(TicketOrder::class, 'order_id');
    }
}

