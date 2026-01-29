<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeletedBillingDetail extends Model
{
    //



    use HasFactory;

    protected $table = 'billing_details_delete';
    protected $fillable = [
        'application_id',
        'billing_company',
        'contact_name',
        'email',
        'phone',
        'address',
        'city_id',
        'state_id',
        'country_id',
        'postal_code',
        'same_as_basic',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
