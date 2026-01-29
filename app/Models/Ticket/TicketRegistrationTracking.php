<?php

namespace App\Models\Ticket;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketRegistrationTracking extends Model
{
    protected $table = 'ticket_registration_tracking';

    protected $fillable = [
        'event_id',
        'tracking_token',
        'session_id',
        'ip_address',
        'user_agent',
        'registration_data',
        'status',
        'started_at',
        'in_progress_at',
        'preview_viewed_at',
        'payment_initiated_at',
        'payment_completed_at',
        'payment_failed_at',
        'abandoned_at',
        'registration_id',
        'order_id',
        'order_no',
        'ticket_type_id',
        'ticket_type_slug',
        'nationality',
        'delegate_count',
        'company_country',
        'calculated_total',
        'final_total',
        'dropoff_stage',
        'dropoff_reason',
    ];

    protected $casts = [
        'registration_data' => 'array',
        'started_at' => 'datetime',
        'in_progress_at' => 'datetime',
        'preview_viewed_at' => 'datetime',
        'payment_initiated_at' => 'datetime',
        'payment_completed_at' => 'datetime',
        'payment_failed_at' => 'datetime',
        'abandoned_at' => 'datetime',
        'calculated_total' => 'decimal:2',
        'final_total' => 'decimal:2',
    ];

    /**
     * Get the event
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Events::class, 'event_id');
    }

    /**
     * Get the registration (if converted)
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(TicketRegistration::class, 'registration_id');
    }

    /**
     * Get the order (if converted)
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(TicketOrder::class, 'order_id');
    }

    /**
     * Generate unique tracking token
     */
    public static function generateTrackingToken(): string
    {
        return 'TRK-' . strtoupper(uniqid()) . '-' . time();
    }

    /**
     * Update status and set corresponding timestamp
     */
    public function updateStatus(string $status, array $additionalData = []): void
    {
        $this->status = $status;
        
        // Set corresponding timestamp
        $timestampField = $status . '_at';
        if (in_array($timestampField, $this->fillable)) {
            $this->$timestampField = now();
        }
        
        // Update additional data
        foreach ($additionalData as $key => $value) {
            if (in_array($key, $this->fillable)) {
                $this->$key = $value;
            }
        }
        
        $this->save();
    }
}
