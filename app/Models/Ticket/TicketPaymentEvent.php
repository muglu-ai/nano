<?php

namespace App\Models\Ticket;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketPaymentEvent extends Model
{
    protected $table = 'ticket_payment_events';

    protected $fillable = [
        'payment_id',
        'event_type', // 'webhook_received', 'webhook_processed', 'manual_update'
        'payload_json',
    ];

    protected $casts = [
        'payload_json' => 'array',
    ];

    /**
     * Get the payment
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(TicketPayment::class, 'payment_id');
    }
}

