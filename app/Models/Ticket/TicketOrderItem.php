<?php

namespace App\Models\Ticket;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketOrderItem extends Model
{
    protected $table = 'ticket_order_items';

    protected $fillable = [
        'order_id',
        'ticket_type_id',
        'selected_event_day_id', // The specific day user selected for this ticket
        'quantity',
        'unit_price',
        'subtotal', // Quantity × unit_price
        'gst_rate', // GST rate percentage (e.g., 18)
        'gst_amount', // GST amount calculated
        'processing_charge_rate', // Processing charge percentage (e.g., 3 or 9)
        'processing_charge_amount', // Processing charge amount
        'total', // subtotal + gst_amount + processing_charge_amount
        'pricing_type', // 'early_bird' or 'regular' - snapshot of pricing used
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'gst_rate' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'processing_charge_rate' => 'decimal:2',
        'processing_charge_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Get the order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(TicketOrder::class, 'order_id');
    }

    /**
     * Get the ticket type
     */
    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class, 'ticket_type_id');
    }

    /**
     * Get the selected event day
     */
    public function selectedDay(): BelongsTo
    {
        return $this->belongsTo(EventDay::class, 'selected_event_day_id');
    }
}

