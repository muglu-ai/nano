<?php

namespace App\Models\Ticket;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketSubcategory extends Model
{
    protected $table = 'ticket_subcategories';

    protected $fillable = [
        'category_id',
        'name', // Member/Non-member/Student
        'description',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Get the category that owns this subcategory
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    /**
     * Get ticket types in this subcategory
     */
    public function ticketTypes(): HasMany
    {
        return $this->hasMany(TicketType::class, 'subcategory_id');
    }
}

