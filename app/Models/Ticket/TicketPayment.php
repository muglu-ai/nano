<?php

namespace App\Models\Ticket;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketPayment extends Model
{
    protected $table = 'ticket_payments';

    protected $fillable = [
        'order_ids_json', // JSON array of order_ids - supports multiple orders
        'method', // 'upi', 'netbanking', 'card', 'manual', 'offline'
        'amount',
        'status', // 'pending', 'processing', 'completed', 'failed', 'refunded'
        'gateway_txn_id',
        'gateway_name', // 'ccavenue', 'paypal', etc.
        'paid_at',
        'pg_request_json', // Full request sent to payment gateway
        'pg_response_json', // Full response received from payment gateway
        'pg_webhook_json', // Webhook payload received
    ];

    protected $casts = [
        'order_ids_json' => 'array',
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'pg_request_json' => 'array',
        'pg_response_json' => 'array',
        'pg_webhook_json' => 'array',
    ];

    /**
     * Get orders for this payment (many-to-many relationship)
     */
    public function getOrdersAttribute()
    {
        $orderIds = $this->order_ids_json ?? [];
        if (empty($orderIds)) {
            return collect([]);
        }
        return TicketOrder::whereIn('id', $orderIds)->get();
    }

    /**
     * Get primary order (first order in the array)
     */
    public function getPrimaryOrderAttribute()
    {
        $orderIds = $this->order_ids_json ?? [];
        if (empty($orderIds)) {
            return null;
        }
        return TicketOrder::find($orderIds[0]);
    }

    /**
     * Add order to payment
     */
    public function addOrder(int $orderId): void
    {
        $orderIds = $this->order_ids_json ?? [];
        if (!in_array($orderId, $orderIds)) {
            $orderIds[] = $orderId;
            $this->order_ids_json = $orderIds;
            $this->save();
        }
    }

    /**
     * Get payment events (webhook logs)
     */
    public function events(): HasMany
    {
        return $this->hasMany(TicketPaymentEvent::class, 'payment_id');
    }
}

