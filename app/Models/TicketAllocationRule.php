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
        return $boothArea >= $this->booth_area_min && $boothArea <= $this->booth_area_max;
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
