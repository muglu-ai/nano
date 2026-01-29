<?php

namespace App\Models\Ticket;

use App\Models\Events;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EventDay extends Model
{
    protected $table = 'event_days';

    protected $fillable = [
        'event_id',
        'label', // e.g., "Day 1", "Day 2", "VIP Day"
        'date',
        'sort_order',
    ];

    protected $casts = [
        'date' => 'date',
        'sort_order' => 'integer',
    ];

    /**
     * Get the event that owns this day
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Events::class, 'event_id');
    }

    /**
     * Get ticket types that have access to this day
     */
    public function ticketTypes(): BelongsToMany
    {
        return $this->belongsToMany(
            TicketType::class,
            'ticket_type_day_access',
            'event_day_id',
            'ticket_type_id'
        );
    }
}

