<?php

namespace App\Models\Ticket;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketTypeDayAccess extends Model
{
    protected $table = 'ticket_type_day_access';

    protected $fillable = [
        'ticket_type_id',
        'event_day_id',
    ];

    /**
     * Get the ticket type
     */
    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class, 'ticket_type_id');
    }

    /**
     * Get the event day
     */
    public function eventDay(): BelongsTo
    {
        return $this->belongsTo(EventDay::class, 'event_day_id');
    }
}

