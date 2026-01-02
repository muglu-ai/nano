<?php

namespace App\Models\Ticket;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketOtpRequest extends Model
{
    protected $table = 'ticket_otp_requests';

    protected $fillable = [
        'contact_id',
        'channel', // 'email', 'sms'
        'otp_hash',
        'expires_at',
        'attempts',
        'status', // 'pending', 'verified', 'expired'
        'ip_address',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'attempts' => 'integer',
    ];

    /**
     * Get the contact
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(TicketContact::class, 'contact_id');
    }

    /**
     * Check if OTP is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && now()->gt($this->expires_at);
    }

    /**
     * Check if OTP is verified
     */
    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }
}

