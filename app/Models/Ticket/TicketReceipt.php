<?php

namespace App\Models\Ticket;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketReceipt extends Model
{
    protected $table = 'ticket_receipts';

    protected $fillable = [
        'registration_id',
        'order_id',
        'type', // 'provisional', 'acknowledgment'
        'receipt_no',
        'file_path',
        'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    /**
     * Get the registration
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(TicketRegistration::class, 'registration_id');
    }

    /**
     * Get the order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(TicketOrder::class, 'order_id');
    }
}

