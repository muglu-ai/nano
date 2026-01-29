<?php

namespace App\Models\Ticket;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketDelegateAssignment extends Model
{
    protected $table = 'ticket_delegate_assignments';

    protected $fillable = [
        'delegate_id',
        'ticket_type_id',
        'subcategory_id',
        'day_access_snapshot_json', // Snapshot of allowed days at time of assignment
        'price_snapshot', // Price at time of assignment (early_bird or regular)
        'pricing_type_snapshot', // 'early_bird' or 'regular' - snapshot of pricing type used
    ];

    protected $casts = [
        'day_access_snapshot_json' => 'array',
        'price_snapshot' => 'decimal:2',
    ];

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

    /**
     * Get the subcategory
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(TicketSubcategory::class, 'subcategory_id');
    }
}

