<?php

namespace App\Models\Ticket;

use App\Models\Events;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class TicketType extends Model
{
    protected $table = 'ticket_types';

    protected $fillable = [
        'event_id',
        'category_id',
        'subcategory_id',
        'name',
        'slug',
        'description',
        'early_bird_price', // Early bird price (legacy, kept for backward compatibility)
        'early_bird_price_national', // Early bird price for national users
        'early_bird_price_international', // Early bird price for international users
        'regular_price', // Regular price after early bird ends (legacy, kept for backward compatibility)
        'regular_price_national', // Regular price for national users
        'regular_price_international', // Regular price for international users
        'per_day_price_national', // Per-day price for national users (INR)
        'per_day_price_international', // Per-day price for international users (USD)
        'early_bird_end_date', // When early bird pricing ends
        'capacity', // null for unlimited
        'sale_start_at',
        'sale_end_at',
        'is_active',
        'available_for', // 'both' | 'indian_only' | 'international_only'
        'all_days_access', // If true, ticket grants access to all event days
        'enable_day_selection', // When enabled, users can select which day(s) they want to attend
        'sort_order',
        'early_bird_reminder_sent', // Track if sales team has been reminded
    ];

    protected $casts = [
        'early_bird_price' => 'decimal:2',
        'early_bird_price_national' => 'decimal:2',
        'early_bird_price_international' => 'decimal:2',
        'regular_price' => 'decimal:2',
        'regular_price_national' => 'decimal:2',
        'regular_price_international' => 'decimal:2',
        'per_day_price_national' => 'decimal:2',
        'per_day_price_international' => 'decimal:2',
        'early_bird_end_date' => 'date',
        'capacity' => 'integer',
        'sale_start_at' => 'datetime',
        'sale_end_at' => 'datetime',
        'is_active' => 'boolean',
        'all_days_access' => 'boolean',
        'enable_day_selection' => 'boolean',
        'sort_order' => 'integer',
        'early_bird_reminder_sent' => 'boolean',
    ];

    /**
     * Get the event that owns this ticket type
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Events::class, 'event_id');
    }

    /**
     * Get the category for this ticket type
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    /**
     * Get the subcategory for this ticket type
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(TicketSubcategory::class, 'subcategory_id');
    }

    /**
     * Get event days this ticket type has access to
     */
    public function eventDays(): BelongsToMany
    {
        return $this->belongsToMany(
            EventDay::class,
            'ticket_type_day_access',
            'ticket_type_id',
            'event_day_id'
        );
    }

    /**
     * Get inventory for this ticket type
     */
    public function inventory(): HasOne
    {
        return $this->hasOne(TicketInventory::class, 'ticket_type_id');
    }

    /**
     * Get early bird reminders for this ticket type
     */
    public function earlyBirdReminders(): HasMany
    {
        return $this->hasMany(TicketEarlyBirdReminder::class, 'ticket_type_id');
    }

    /**
     * Check if this ticket type is available for the given audience (national = Indian, international = International)
     *
     * @param string $nationality 'national' or 'international'
     * @return bool
     */
    public function isAvailableFor(string $nationality): bool
    {
        $for = $this->available_for ?? 'both';
        if ($for === 'both') {
            return true;
        }
        if ($for === 'indian_only') {
            return $nationality === 'national';
        }
        if ($for === 'international_only') {
            return $nationality === 'international';
        }
        return true;
    }

    /**
     * Check if ticket is currently on sale
     */
    public function isOnSale(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        
        if ($this->sale_start_at && $now->lt($this->sale_start_at)) {
            return false;
        }

        if ($this->sale_end_at && $now->gt($this->sale_end_at)) {
            return false;
        }

        return true;
    }

    /**
     * Get current price based on early bird status and nationality
     * 
     * @param string $nationality 'national' or 'international'
     * @return float
     */
    public function getCurrentPrice(string $nationality = 'national'): float
    {
        $isEarlyBird = $this->early_bird_end_date && now()->lte($this->early_bird_end_date);
        
        if ($nationality === 'international') {
            if ($isEarlyBird && $this->early_bird_price_international) {
                return (float) $this->early_bird_price_international;
            }
            return (float) ($this->regular_price_international ?? 0);
        } else {
            // National pricing
            if ($isEarlyBird && $this->early_bird_price_national) {
                return (float) $this->early_bird_price_national;
            }
            return (float) ($this->regular_price_national ?? 0);
        }
    }
    
    /**
     * Get early bird price for a specific nationality
     * 
     * @param string $nationality 'national' or 'international'
     * @return float|null
     */
    public function getEarlyBirdPrice(string $nationality = 'national'): ?float
    {
        if ($nationality === 'international') {
            return $this->early_bird_price_international ? (float) $this->early_bird_price_international : null;
        }
        return $this->early_bird_price_national ? (float) $this->early_bird_price_national : null;
    }
    
    /**
     * Get regular price for a specific nationality
     * 
     * @param string $nationality 'national' or 'international'
     * @return float
     */
    public function getRegularPrice(string $nationality = 'national'): float
    {
        if ($nationality === 'international') {
            return (float) ($this->regular_price_international ?? 0);
        }
        return (float) ($this->regular_price_national ?? 0);
    }

    /**
     * Get per-day price for a specific nationality
     * 
     * @param string $nationality 'national' or 'international'
     * @return float|null Returns null if per-day pricing is not set
     */
    public function getPerDayPrice(string $nationality = 'national'): ?float
    {
        if ($nationality === 'international') {
            return $this->per_day_price_international ? (float) $this->per_day_price_international : null;
        }
        return $this->per_day_price_national ? (float) $this->per_day_price_national : null;
    }

    /**
     * Check if per-day pricing is enabled for this ticket type
     * 
     * @return bool
     */
    public function hasPerDayPricing(): bool
    {
        return $this->per_day_price_national !== null || $this->per_day_price_international !== null;
    }

    /**
     * Check if early bird pricing is active
     */
    public function isEarlyBirdActive(): bool
    {
        if (!$this->early_bird_end_date) {
            return false;
        }
        return now()->lte($this->early_bird_end_date);
    }

    /**
     * Check if early bird reminder should be sent (within 7 days of end date)
     */
    public function shouldSendEarlyBirdReminder(): bool
    {
        if (!$this->early_bird_end_date || $this->early_bird_reminder_sent) {
            return false;
        }
        
        $daysUntilEnd = now()->diffInDays($this->early_bird_end_date, false);
        return $daysUntilEnd <= 7 && $daysUntilEnd >= 0;
    }

    /**
     * Get available quantity
     */
    public function getAvailableQuantity(): ?int
    {
        if ($this->capacity === null) {
            return null; // Unlimited
        }

        $inventory = $this->inventory;
        if (!$inventory) {
            return $this->capacity;
        }

        return $this->capacity - $inventory->reserved_qty - $inventory->sold_qty;
    }

    /**
     * Check if ticket is sold out
     */
    public function isSoldOut(): bool
    {
        if ($this->capacity === null) {
            return false; // Unlimited
        }

        return $this->getAvailableQuantity() <= 0;
    }

    /**
     * Get all accessible event days for this ticket type
     * Returns the specific days assigned via the pivot table
     */
    public function getAllAccessibleDays()
    {
        return $this->eventDays()->orderBy('sort_order')->orderBy('date')->get();
    }
    
    /**
     * Get all event days for the event (used when day selection is disabled)
     */
    public function getAllEventDays()
    {
            return EventDay::where('event_id', $this->event_id)
                ->orderBy('sort_order')
                ->orderBy('date')
                ->get();
    }

    /**
     * Check if ticket has access to a specific day
     */
    public function hasAccessToDay($dayId): bool
    {
        if ($this->all_days_access) {
            return EventDay::where('event_id', $this->event_id)
                ->where('id', $dayId)
                ->exists();
        }
        
        return $this->eventDays()->where('event_days.id', $dayId)->exists();
    }
    
    /**
     * Boot method to auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($ticketType) {
            if (empty($ticketType->slug)) {
                $baseSlug = Str::slug($ticketType->name);
                $slug = $baseSlug;
                $counter = 1;
                
                // Ensure uniqueness within the event
                while (static::where('event_id', $ticketType->event_id)
                    ->where('slug', $slug)
                    ->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                
                $ticketType->slug = $slug;
            }
        });
        
        static::updating(function ($ticketType) {
            // If name changed and slug is empty or matches old name, regenerate slug
            if ($ticketType->isDirty('name') && (empty($ticketType->slug) || $ticketType->getOriginal('slug') === Str::slug($ticketType->getOriginal('name')))) {
                $baseSlug = Str::slug($ticketType->name);
                $slug = $baseSlug;
                $counter = 1;
                
                // Ensure uniqueness within the event
                while (static::where('event_id', $ticketType->event_id)
                    ->where('slug', $slug)
                    ->where('id', '!=', $ticketType->id)
                    ->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                
                $ticketType->slug = $slug;
            }
        });
    }
}

