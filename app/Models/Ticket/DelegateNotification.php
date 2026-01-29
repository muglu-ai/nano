<?php

namespace App\Models\Ticket;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DelegateNotification extends Model
{
    protected $table = 'delegate_notifications';

    protected $fillable = [
        'delegate_id',
        'contact_id',
        'title',
        'message',
        'type',
        'is_read',
        'read_at',
        'created_by',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Get the delegate
     */
    public function delegate(): BelongsTo
    {
        return $this->belongsTo(TicketDelegate::class, 'delegate_id');
    }

    /**
     * Get the contact
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(TicketContact::class, 'contact_id');
    }

    /**
     * Get the admin user who created this notification
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Mark as read
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }
}
