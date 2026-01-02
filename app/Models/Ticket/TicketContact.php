<?php

namespace App\Models\Ticket;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketContact extends Model
{
    protected $table = 'ticket_contacts';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'email_verified_at',
        'phone_verified_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
    ];

    /**
     * Get the optional account for this contact
     */
    public function account(): HasOne
    {
        return $this->hasOne(TicketAccount::class, 'contact_id');
    }

    /**
     * Get registrations for this contact
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(TicketRegistration::class, 'contact_id');
    }

    /**
     * Get OTP requests for this contact
     */
    public function otpRequests(): HasMany
    {
        return $this->hasMany(TicketOtpRequest::class, 'contact_id');
    }

    /**
     * Get magic links for this contact
     */
    public function magicLinks(): HasMany
    {
        return $this->hasMany(TicketMagicLink::class, 'contact_id');
    }

    /**
     * Check if email is verified
     */
    public function isEmailVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    /**
     * Check if phone is verified
     */
    public function isPhoneVerified(): bool
    {
        return $this->phone_verified_at !== null;
    }
}

