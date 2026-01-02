<?php

namespace App\Models\Ticket;

use App\Models\Events;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketPromoCode extends Model
{
    protected $table = 'ticket_promo_codes';

    protected $fillable = [
        'event_id',
        'code',
        'type', // 'percentage', 'fixed'
        'value', // Discount percentage or fixed amount
        'valid_from',
        'valid_to',
        'max_uses', // null for unlimited
        'max_uses_per_contact', // null for unlimited
        'min_order_amount', // Minimum order amount to apply
        'applicable_ticket_ids_json', // JSON array of ticket_type_ids (null for all)
        'rules_json', // Additional rules
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
        'max_uses' => 'integer',
        'max_uses_per_contact' => 'integer',
        'min_order_amount' => 'decimal:2',
        'applicable_ticket_ids_json' => 'array',
        'rules_json' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the event
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Events::class, 'event_id');
    }

    /**
     * Get redemptions for this promo code
     */
    public function redemptions(): HasMany
    {
        return $this->hasMany(TicketPromoRedemption::class, 'promo_id');
    }

    /**
     * Check if promo code is valid
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        if ($this->valid_from && $now->lt($this->valid_from)) {
            return false;
        }

        if ($this->valid_to && $now->gt($this->valid_to)) {
            return false;
        }

        if ($this->max_uses !== null) {
            $usedCount = $this->redemptions()->count();
            if ($usedCount >= $this->max_uses) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount(float $orderAmount): float
    {
        if ($this->type === 'percentage') {
            return ($orderAmount * $this->value) / 100;
        } else {
            return min($this->value, $orderAmount); // Fixed amount, but not more than order
        }
    }
}

