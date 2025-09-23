<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'application_id',
        'sponsorship_id',
        'amount',
        'int_amount_value',
        'usd_rate',
        'paid_amount',
        'currency',
        'payment_status',
        'payment_due_date',
        'discount_per',
        'discount',
        'gst',
        'price',
        'processing_chargesRate',
        'processing_charges',
        'final_total_price',
        'partial_payment_percentage',
        'pending_amount',
        'type',
        'invoice_no',
        'pin_no',
        'amount_paid',
        'total_final_price',
        'co_exhibitorID',
        'rate',
        'tax_invoice',
        'remarks',
        'surCharge',
        'surChargepercentage',
        'surChargeRemove',
        'removeProcessing',
        'tdsReason',
        'surChargeLock',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function billingDetail()
    {
        return $this->hasOne(BillingDetail::class);
    }

    public function sponsorship()
    {
        return $this->belongsTo(Sponsorship::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    //make relation with BillingDetails model who has same application id
    public function billingDetails2()
    {
        return $this->hasOne(BillingDetail::class, 'application_id', 'application_id');
    }

    public function billingDetails()
    {
        return $this->belongsTo(BillingDetail::class, 'application_id', 'application_id');
    }

    public function requirementsOrder()
    {
        return $this->hasOne(RequirementsOrder::class);
    }

    public function orders()
    {
        return $this->hasMany(RequirementsOrder::class, 'invoice_id', 'id');
    }

    //payment where invoice type =Stall Booking then get the latest payment which status is successful
    public function stallBookingPayment()
    {
        return $this->hasOne(Payment::class)->where('status', 'Successful')->whereHas('invoice', function ($query) {
            $query->where('type', 'Stall Booking');
        })->latestOfMany();
    }
}
