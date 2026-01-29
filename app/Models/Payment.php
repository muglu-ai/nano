<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'order_id',
        'payment_method',
        'amount',
        'amount_paid',
        'amount_received',
        'transaction_id',
        'pg_result',
        'track_id',
        'response',
        'pg_response_json',
        'payment_date',
        'currency',
        'status',
        'rejection_reason',
        'receipt_image',
        'verification_status',
        'user_id',
        'verified_by',
        'verified_at',
        'remarks',
        'tds_amount',
        'tdsReason',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
