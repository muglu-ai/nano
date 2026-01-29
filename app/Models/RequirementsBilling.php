<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequirementsBilling extends Model
{
    protected $fillable = [
        'invoice_id',
        'billing_company',
        'billing_name',
        'billing_email',
        'billing_phone',
        'gst_no',
        'pan_no',
        'billing_address',
        'country_id',
        'state_id',
        'zipcode',
        'billing_city',

    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
