<?php

namespace App\Models\Ticket;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketEarlyBirdReminder extends Model
{
    protected $table = 'ticket_early_bird_reminders';

    protected $fillable = [
        'ticket_type_id',
        'reminder_date',
        'reminded_to_user_id', // Portal user (sales team member) who was reminded
        'reminder_sent_at',
        'reminder_type', // 'email', 'notification', 'both'
    ];

    protected $casts = [
        'reminder_date' => 'date',
        'reminder_sent_at' => 'datetime',
    ];

    /**
     * Get the ticket type
     */
    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class, 'ticket_type_id');
    }

    /**
     * Get the user who was reminded
     */
    public function remindedToUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reminded_to_user_id');
    }
}

