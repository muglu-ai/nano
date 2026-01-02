<?php

namespace App\Models\Ticket;

use App\Models\Events;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketCategory extends Model
{
    protected $table = 'ticket_categories';

    protected $fillable = [
        'event_id',
        'name', // Delegate/VIP/Workshop
        'description',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Get the event that owns this category
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Events::class, 'event_id');
    }

    /**
     * Get subcategories for this category
     */
    public function subcategories(): HasMany
    {
        return $this->hasMany(TicketSubcategory::class, 'category_id');
    }

    /**
     * Get ticket types in this category
     */
    public function ticketTypes(): HasMany
    {
        return $this->hasMany(TicketType::class, 'category_id');
    }
}

