<?php

namespace App\Models\Ticket;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketUpgradeRequest extends Model
{
    protected $table = 'ticket_upgrade_requests';

    protected $fillable = [
        'request_type',
        'contact_id',
        'registration_id',
        'upgrade_data_json',
        'price_difference',
        'gst_amount',
        'processing_charge_amount',
        'total_amount',
        'upgrade_order_id',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'upgrade_data_json' => 'array',
        'price_difference' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'processing_charge_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the contact
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(TicketContact::class, 'contact_id');
    }

    /**
     * Get the registration
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(TicketRegistration::class, 'registration_id');
    }

    /**
     * Get the upgrade order
     */
    public function upgradeOrder(): BelongsTo
    {
        return $this->belongsTo(TicketOrder::class, 'upgrade_order_id');
    }

    /**
     * Get upgrades for this request
     */
    public function upgrades(): HasMany
    {
        return $this->hasMany(TicketUpgrade::class, 'upgrade_request_id');
    }

    /**
     * Check if request is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && now()->gt($this->expires_at);
    }

    /**
     * Check if request can be processed
     */
    public function canBeProcessed(): bool
    {
        return $this->status === 'pending' && !$this->isExpired();
    }

    /**
     * Mark as paid
     */
    public function markAsPaid(): void
    {
        $this->update(['status' => 'paid']);
    }

    /**
     * Mark as failed
     */
    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

    /**
     * Mark as cancelled
     */
    public function markAsCancelled(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for active (not expired) requests
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        });
    }
}
