<?php

namespace App\Models\Ticket;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketAssociationLink extends Model
{
    protected $table = 'ticket_association_links';

    protected $fillable = [
        'allocation_id',
        'token',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Get the allocation
     */
    public function allocation(): BelongsTo
    {
        return $this->belongsTo(TicketAssociationAllocation::class, 'allocation_id');
    }

    /**
     * Check if link is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && now()->gt($this->expires_at);
    }
}

