<?php

namespace App\Models\Ticket;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketUpgrade extends Model
{
    protected $table = 'ticket_upgrades';

    protected $fillable = [
        'upgrade_request_id',
        'contact_id',
        'old_ticket_id',
        'new_ticket_id',
        'upgrade_order_id',
    ];

    /**
     * Get the upgrade request
     */
    public function upgradeRequest(): BelongsTo
    {
        return $this->belongsTo(TicketUpgradeRequest::class, 'upgrade_request_id');
    }

    /**
     * Get the contact
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(TicketContact::class, 'contact_id');
    }

    /**
     * Get the old ticket
     */
    public function oldTicket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'old_ticket_id');
    }

    /**
     * Get the new ticket
     */
    public function newTicket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'new_ticket_id');
    }

    /**
     * Get the upgrade order
     */
    public function upgradeOrder(): BelongsTo
    {
        return $this->belongsTo(TicketOrder::class, 'upgrade_order_id');
    }
}
