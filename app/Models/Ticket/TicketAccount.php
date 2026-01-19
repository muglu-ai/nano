<?php

namespace App\Models\Ticket;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class TicketAccount extends Authenticatable
{
    use Notifiable;

    protected $table = 'ticket_accounts';

    protected $fillable = [
        'contact_id',
        'password',
        'email_verified_at',
        'remember_token',
        'status', // 'active', 'suspended', 'inactive'
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the contact that owns this account
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(TicketContact::class, 'contact_id');
    }

    /**
     * Get the email address for authentication
     */
    public function getEmailAttribute(): ?string
    {
        return $this->contact?->email;
    }

    /**
     * Get the name for authentication
     */
    public function getNameAttribute(): ?string
    {
        return $this->contact?->name;
    }


    /**
     * Check if account is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if email is verified
     */
    public function isEmailVerified(): bool
    {
        return $this->email_verified_at !== null;
    }
}

