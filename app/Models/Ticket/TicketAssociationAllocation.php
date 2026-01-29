<?php

namespace App\Models\Ticket;

use App\Models\Events;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class TicketAssociationAllocation extends Model
{
    protected $table = 'ticket_association_allocations';

    protected $fillable = [
        'association_id',
        'event_id',
        'ticket_type_id',
        'allocated_qty',
        'used_qty',
    ];

    protected $casts = [
        'allocated_qty' => 'integer',
        'used_qty' => 'integer',
    ];

    /**
     * Get the association
     */
    public function association(): BelongsTo
    {
        return $this->belongsTo(TicketAssociation::class, 'association_id');
    }

    /**
     * Get the event
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Events::class, 'event_id');
    }

    /**
     * Get the ticket type
     */
    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class, 'ticket_type_id');
    }

    /**
     * Get shareable links for this allocation
     */
    public function links(): HasMany
    {
        return $this->hasMany(TicketAssociationLink::class, 'allocation_id');
    }

    /**
     * Get remaining quantity
     */
    public function getRemainingQtyAttribute(): int
    {
        return $this->allocated_qty - $this->used_qty;
    }

    /**
     * Use quota atomically
     */
    public static function useQuota(int $allocationId, int $quantity): bool
    {
        return DB::transaction(function () use ($allocationId, $quantity) {
            $allocation = self::lockForUpdate()->find($allocationId);
            
            if (!$allocation) {
                return false;
            }

            if ($allocation->used_qty + $quantity > $allocation->allocated_qty) {
                return false; // Not enough quota
            }

            $allocation->increment('used_qty', $quantity);
            return true;
        });
    }
}

