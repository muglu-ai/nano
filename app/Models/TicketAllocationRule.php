<?php

namespace App\Models;

use App\Models\Events;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketAllocationRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'application_type',
        'booth_type',
        'booth_area_min',
        'booth_area_max',
        'ticket_allocations',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'ticket_allocations' => 'array',
        'is_active' => 'boolean',
        'booth_area_min' => 'integer',
        'booth_area_max' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Get the event that owns this allocation rule
     */
    public function event()
    {
        return $this->belongsTo(Events::class, 'event_id');
    }

    /**
     * Check if a booth area falls within this rule's range
     */
    public function matchesBoothArea(float $boothArea): bool
    {
        // For special booth types, use exact match
        if ($this->booth_type) {
            return false; // Special types are matched by string, not numeric
        }
        return $boothArea >= $this->booth_area_min && $boothArea <= $this->booth_area_max;
    }

    /**
     * Check if a special booth type matches this rule
     */
    public function matchesBoothType(string $boothType): bool
    {
        return $this->booth_type && strcasecmp(trim($this->booth_type), trim($boothType)) === 0;
    }

    /**
     * Scope to get active rules
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by event
     */
    public function scopeForEvent($query, ?int $eventId)
    {
        if ($eventId) {
            return $query->where('event_id', $eventId);
        }
        return $query->whereNull('event_id');
    }

    /**
     * Scope to filter by application type
     */
    public function scopeForApplicationType($query, ?string $applicationType)
    {
        if ($applicationType) {
            return $query->where('application_type', $applicationType);
        }
        return $query->whereNull('application_type');
    }
}
