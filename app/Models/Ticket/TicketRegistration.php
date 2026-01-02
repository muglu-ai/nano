<?php

namespace App\Models\Ticket;

use App\Models\Events;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TicketRegistration extends Model
{
    protected $table = 'ticket_registrations';

    protected $fillable = [
        'event_id',
        'contact_id',
        'company_name',
        'company_country',
        'company_state',
        'company_city',
        'company_phone',
        'industry_sector',
        'organisation_type',
        'registration_category_id',
        'gst_required',
        'gstin',
        'gst_legal_name',
        'gst_address',
        'gst_state',
        'nationality',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'ref_source_type', // 'association', 'admin_invite', 'promo', null
        'ref_source_id', // ID of association, invite, etc.
    ];

    protected $casts = [
        'gst_required' => 'boolean',
    ];

    /**
     * Get the event
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Events::class, 'event_id');
    }

    /**
     * Get the contact
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(TicketContact::class, 'contact_id');
    }

    /**
     * Get the registration category
     */
    public function registrationCategory(): BelongsTo
    {
        return $this->belongsTo(TicketRegistrationCategory::class, 'registration_category_id');
    }

    /**
     * Get delegates for this registration
     */
    public function delegates(): HasMany
    {
        return $this->hasMany(TicketDelegate::class, 'registration_id');
    }

    /**
     * Get the order for this registration
     */
    public function order(): HasOne
    {
        return $this->hasOne(TicketOrder::class, 'registration_id');
    }
}

