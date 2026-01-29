<?php

namespace App\Models\Ticket;

use App\Models\Events;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketRegistrationCategory extends Model
{
    protected $table = 'ticket_registration_categories';

    protected $fillable = [
        'event_id',
        'name', // Delegate/Visitor/VIP/Student
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the event that owns this registration category
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Events::class, 'event_id');
    }

    /**
     * Get ticket rules for this registration category
     */
    public function ticketRules(): HasMany
    {
        return $this->hasMany(TicketCategoryTicketRule::class, 'registration_category_id');
    }
}

