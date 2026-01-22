<?php

namespace App\Models\Ticket;

use App\Models\Events;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class TicketPromoCode extends Model
{
    protected $table = 'ticket_promo_codes';

    protected $fillable = [
        'event_id',
        'code',
        'organization_name', // Organization binding for tracking
        'type', // 'percentage', 'fixed'
        'value', // Discount percentage or fixed amount
        'valid_from',
        'valid_to',
        'max_uses', // null for unlimited
        'max_uses_per_contact', // null for unlimited
        'min_order_amount', // Minimum order amount to apply
        'applicable_ticket_ids_json', // JSON array of ticket_type_ids (null for all)
        'applicable_registration_category_ids_json', // JSON array of registration category IDs (null = all)
        'applicable_ticket_category_ids_json', // JSON array of ticket category IDs (null = all)
        'applicable_event_day_ids_json', // JSON array of event day IDs (null = all days)
        'max_delegates', // Maximum number of delegates allowed (null = unlimited)
        'min_delegates', // Minimum number of delegates required
        'apply_to_base_amount_only', // Boolean - ensures discount only on base amount
        'description', // Text field for admin notes
        'created_by', // Admin user ID who created the promocode
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
        'max_delegates' => 'integer',
        'min_delegates' => 'integer',
        'apply_to_base_amount_only' => 'boolean',
        'applicable_ticket_ids_json' => 'array',
        'applicable_registration_category_ids_json' => 'array',
        'applicable_ticket_category_ids_json' => 'array',
        'applicable_event_day_ids_json' => 'array',
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
     * Get the user who created this promocode
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get applicable registration categories
     */
    public function applicableRegistrationCategories()
    {
        if (!$this->applicable_registration_category_ids_json) {
            return TicketRegistrationCategory::where('event_id', $this->event_id)->get();
        }
        return TicketRegistrationCategory::whereIn('id', $this->applicable_registration_category_ids_json)->get();
    }

    /**
     * Get applicable ticket categories
     */
    public function applicableTicketCategories()
    {
        if (!$this->applicable_ticket_category_ids_json) {
            return TicketCategory::where('event_id', $this->event_id)->get();
        }
        return TicketCategory::whereIn('id', $this->applicable_ticket_category_ids_json)->get();
    }

    /**
     * Get applicable event days
     */
    public function applicableEventDays()
    {
        if (!$this->applicable_event_day_ids_json) {
            return EventDay::where('event_id', $this->event_id)->get();
        }
        return EventDay::whereIn('id', $this->applicable_event_day_ids_json)->get();
    }

    /**
     * Scope to filter by organization name
     */
    public function scopeByOrganization(Builder $query, string $organizationName): Builder
    {
        return $query->where('organization_name', $organizationName);
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
            $usedCount = $this->getUsedCount();
            if ($usedCount >= $this->max_uses) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if valid for registration category
     */
    public function isValidForCategory(?int $categoryId): bool
    {
        if (!$this->applicable_registration_category_ids_json) {
            return true; // No restriction, valid for all
        }
        return in_array($categoryId, $this->applicable_registration_category_ids_json);
    }

    /**
     * Check if valid for ticket category
     */
    public function isValidForTicketCategory(?int $ticketCategoryId): bool
    {
        if (!$this->applicable_ticket_category_ids_json) {
            return true; // No restriction, valid for all
        }
        return in_array($ticketCategoryId, $this->applicable_ticket_category_ids_json);
    }

    /**
     * Check if valid for event day
     */
    public function isValidForDay(?int $eventDayId): bool
    {
        if (!$this->applicable_event_day_ids_json) {
            return true; // No restriction, valid for all days
        }
        // If eventDayId is null, it means "all days" - check if promocode allows all days
        if ($eventDayId === null) {
            return true; // All days access is always valid
        }
        return in_array($eventDayId, $this->applicable_event_day_ids_json);
    }

    /**
     * Check if delegate count is within limits
     */
    public function isValidForDelegateCount(int $count): bool
    {
        if ($this->min_delegates && $count < $this->min_delegates) {
            return false;
        }
        if ($this->max_delegates && $count > $this->max_delegates) {
            return false;
        }
        return true;
    }

    /**
     * Check if discount is 100% (complimentary)
     */
    public function isComplimentary(float $baseAmount): bool
    {
        $discount = $this->calculateDiscount($baseAmount);
        return $discount >= $baseAmount;
    }

    /**
     * Get used count
     */
    public function getUsedCount(): int
    {
        return $this->redemptions()->count();
    }

    /**
     * Get remaining uses
     */
    public function getRemainingUses(): ?int
    {
        if ($this->max_uses === null) {
            return null; // Unlimited
        }
        return max(0, $this->max_uses - $this->getUsedCount());
    }

    /**
     * Calculate discount amount
     * Note: This applies discount to base amount only (before GST/processing charges)
     */
    public function calculateDiscount(float $baseAmount): float
    {
        if ($this->type === 'percentage') {
            $discount = ($baseAmount * $this->value) / 100;
            // For 100% discount, ensure we don't exceed base amount
            return min($discount, $baseAmount);
        } else {
            // Fixed amount, but not more than base amount
            return min($this->value, $baseAmount);
        }
    }
}

