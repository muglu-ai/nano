<?php

namespace App\Models\Ticket;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class TicketInventory extends Model
{
    protected $table = 'ticket_inventory';

    protected $fillable = [
        'ticket_type_id',
        'reserved_qty',
        'sold_qty',
    ];

    protected $casts = [
        'reserved_qty' => 'integer',
        'sold_qty' => 'integer',
    ];

    /**
     * Get the ticket type
     */
    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class, 'ticket_type_id');
    }

    /**
     * Get available quantity
     */
    public function getAvailableQtyAttribute(): int
    {
        $capacity = $this->ticketType->capacity;
        if ($capacity === null) {
            return PHP_INT_MAX; // Unlimited
        }
        return $capacity - $this->reserved_qty - $this->sold_qty;
    }

    /**
     * Reserve tickets atomically
     */
    public static function reserve(int $ticketTypeId, int $quantity): bool
    {
        return DB::transaction(function () use ($ticketTypeId, $quantity) {
            $inventory = self::lockForUpdate()
                ->where('ticket_type_id', $ticketTypeId)
                ->first();

            if (!$inventory) {
                $inventory = self::create([
                    'ticket_type_id' => $ticketTypeId,
                    'reserved_qty' => 0,
                    'sold_qty' => 0,
                ]);
            }

            $ticketType = TicketType::find($ticketTypeId);
            $capacity = $ticketType->capacity;

            if ($capacity !== null) {
                $available = $capacity - $inventory->reserved_qty - $inventory->sold_qty;
                if ($available < $quantity) {
                    return false; // Not enough available
                }
            }

            $inventory->increment('reserved_qty', $quantity);
            return true;
        });
    }

    /**
     * Release reserved tickets
     */
    public static function release(int $ticketTypeId, int $quantity): void
    {
        DB::transaction(function () use ($ticketTypeId, $quantity) {
            $inventory = self::lockForUpdate()
                ->where('ticket_type_id', $ticketTypeId)
                ->first();

            if ($inventory) {
                $inventory->decrement('reserved_qty', $quantity);
            }
        });
    }

    /**
     * Mark reserved tickets as sold
     */
    public static function markAsSold(int $ticketTypeId, int $quantity): void
    {
        DB::transaction(function () use ($ticketTypeId, $quantity) {
            $inventory = self::lockForUpdate()
                ->where('ticket_type_id', $ticketTypeId)
                ->first();

            if ($inventory) {
                $inventory->decrement('reserved_qty', $quantity);
                $inventory->increment('sold_qty', $quantity);
            }
        });
    }
}

