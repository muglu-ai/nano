<?php

namespace App\Models\Ticket;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketAccount extends Model
{
    protected $table = 'ticket_accounts';

    protected $fillable = [
        'contact_id',
        'status', // 'active', 'suspended', 'inactive'
        'last_login_at',
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
    ];

    /**
     * Get the contact that owns this account
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(TicketContact::class, 'contact_id');
    }
}

