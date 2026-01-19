<?php

namespace App\Models\Ticket;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TicketDelegate extends Model
{
    protected $table = 'ticket_delegates';

    protected $fillable = [
        'registration_id',
        'salutation',
        'first_name',
        'last_name',
        'email',
        'phone',
        'job_title',
    ];

    /**
     * Get the registration
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(TicketRegistration::class, 'registration_id');
    }

    /**
     * Get the ticket assignment for this delegate
     */
    public function assignment(): HasOne
    {
        return $this->hasOne(TicketDelegateAssignment::class, 'delegate_id');
    }

    /**
     * Get the issued ticket for this delegate
     */
    public function ticket(): HasOne
    {
        return $this->hasOne(Ticket::class, 'delegate_id');
    }

    /**
     * Get full name
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->salutation} {$this->first_name} {$this->last_name}");
    }

    /**
     * Get notifications for this delegate
     */
    public function notifications()
    {
        return $this->hasMany(\App\Models\Ticket\DelegateNotification::class, 'delegate_id');
    }
}

