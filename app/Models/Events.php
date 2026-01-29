<?php

namespace App\Models;

use App\Models\Ticket\TicketEventConfig;
use App\Models\Ticket\EventDay;
use App\Models\Ticket\TicketRegistrationCategory;
use App\Models\Ticket\TicketCategory;
use App\Models\Ticket\TicketType;
use App\Models\Ticket\TicketRegistration;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Events extends Model
{
    //
    protected $table = 'events';
    protected $fillable = [
        'event_year',
        'event_name',
        'event_date',
        'event_location',
        'event_description',
        'event_image',
        'start_date',
        'end_date',
        'slug',
        'status',
    ];

    /**
     * Get ticket event configuration
     */
    public function ticketConfig(): HasOne
    {
        return $this->hasOne(TicketEventConfig::class, 'event_id');
    }

    /**
     * Get event days for ticketing
     */
    public function days(): HasMany
    {
        return $this->hasMany(EventDay::class, 'event_id');
    }

    /**
     * Get registration categories
     */
    public function ticketRegistrationCategories(): HasMany
    {
        return $this->hasMany(TicketRegistrationCategory::class, 'event_id');
    }

    /**
     * Get ticket categories
     */
    public function ticketCategories(): HasMany
    {
        return $this->hasMany(TicketCategory::class, 'event_id');
    }

    /**
     * Get ticket types
     */
    public function ticketTypes(): HasMany
    {
        return $this->hasMany(TicketType::class, 'event_id');
    }

    /**
     * Get ticket registrations
     */
    public function ticketRegistrations(): HasMany
    {
        return $this->hasMany(TicketRegistration::class, 'event_id');
    }
}
