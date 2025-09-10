<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentReceipt extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'user_id',
        'payment_method',
        'transaction_id',
        'amount_paid',
        'currency',
        'receipt_image',
        'payment_date',
        'status',
        'rejection_reason'
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
