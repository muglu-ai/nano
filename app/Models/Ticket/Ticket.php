<?php

namespace App\Models\Ticket;

use App\Models\Events;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    protected $table = 'tickets';

    protected $fillable = [
        'event_id',
        'delegate_id',
        'ticket_type_id',
        'status', // 'pending', 'issued', 'cancelled', 'upgraded'
        'access_snapshot_json', // Snapshot of day access at issuance
        'source_type', // 'regular', 'association', 'admin_invite', 'promo'
        'source_id', // ID of association, invite, etc.
    ];

    protected $casts = [
        'access_snapshot_json' => 'array',
    ];

    /**
     * Get the event
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Events::class, 'event_id');
    }

    /**
     * Get the delegate
     */
    public function delegate(): BelongsTo
    {
        return $this->belongsTo(TicketDelegate::class, 'delegate_id');
    }

    /**
     * Get the ticket type
     */
    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class, 'ticket_type_id');
    }
}

