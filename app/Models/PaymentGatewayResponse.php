<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class PaymentGatewayResponse extends Model
{
    protected $table = 'payment_gateway_response';

    protected $fillable = [
        'order_id',
        'payment_id',
        'invoice_id',
        'currency',
        'gateway',
        'amount',
        'amount_received',
        'transaction_id',
        'reference_id',
        'email',
        'status',
        'response_json',
    ];

    protected $casts = [
        'response_json' => 'array',
        'amount' => 'decimal:2',
        'amount_received' => 'decimal:2',
    ];

    public $timestamps = true;
}


