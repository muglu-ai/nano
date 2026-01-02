<?php

namespace App\Models\Ticket;

use App\Models\Events;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketEventConfig extends Model
{
    protected $table = 'ticket_events_config';

    protected $fillable = [
        'event_id',
        'auth_policy', // 'guest', 'otp_required', 'login_required'
        'selection_mode', // 'same_ticket', 'per_delegate'
        'allow_subcategory',
        'allow_day_select',
        'email_cc_json', // JSON array of email addresses
        'receipt_pattern', // e.g., "TKT-{event}-{year}-{seq}"
        'is_active', // Admin can disable registration
    ];

    protected $casts = [
        'allow_subcategory' => 'boolean',
        'allow_day_select' => 'boolean',
        'is_active' => 'boolean',
        'email_cc_json' => 'array',
    ];

    /**
     * Get the event that owns this configuration
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Events::class, 'event_id');
    }

    /**
     * Get email CC list as array
     */
    public function getEmailCcList(): array
    {
        return $this->email_cc_json ?? [];
    }

    /**
     * Check if event setup is complete
     */
    public function isSetupComplete(): bool
    {
        // Check if all required configuration exists
        $hasDays = $this->event->days()->exists();
        $hasRegistrationCategories = $this->event->ticketRegistrationCategories()->exists();
        $hasTicketCategories = $this->event->ticketCategories()->exists();
        $hasTicketTypes = $this->event->ticketTypes()->exists();

        return $hasDays && $hasRegistrationCategories && $hasTicketCategories && $hasTicketTypes;
    }
}

