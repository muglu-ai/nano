<?php

namespace App\Models\Ticket;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketCategoryTicketRule extends Model
{
    protected $table = 'ticket_category_ticket_rules';

    protected $fillable = [
        'registration_category_id',
        'ticket_type_id',
        'subcategory_id',
        'allowed_days_json', // JSON array of event_day_ids
    ];

    protected $casts = [
        'allowed_days_json' => 'array',
    ];

    /**
     * Get the registration category
     */
    public function registrationCategory(): BelongsTo
    {
        return $this->belongsTo(TicketRegistrationCategory::class, 'registration_category_id');
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

    /**
     * Get allowed days as array
     */
    public function getAllowedDays(): array
    {
        return $this->allowed_days_json ?? [];
    }
}

