<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Country;
use App\Models\State;

class Attendee extends Model
{
    use HasFactory;

    protected $table = 'attendees';

    protected $fillable = [
        'unique_id',
        'status',
        'badge_category',
        'title',
        'first_name',
        'middle_name',
        'last_name',
        'designation',
        'company',
        'address',
        'country',
        'state',
        'city',
        'postal_code',
        'mobile',
        'email',
        'purpose',
        'products',
        'business_nature',
        'job_function',
        'job_category',
        'job_subcategory',
        'profile_picture',
        'id_card_type',
        'id_card_number',
        'consent',
        'qr_code_path',
        'source',
        'inaugural_session',
        'registration_type',
        'event_days',
        'other_job_category',
        'promotion_consent',
        'startup',
        'approvedHistory',
        'inauguralConfirmation',
        'updatedBy',
        'api_sent',
        'api_response',
        'api_data',
        'approvedCate',
        'regId',
        'lunchStatus',
        'emailSent'
    ];

    protected $casts = [
        'purpose' => 'array',
        'products' => 'array',
        'consent' => 'boolean',
    ];

    //country name 


    public function countryRelation()
    {
        return $this->belongsTo(Country::class, 'country', 'id');
    }
    //state name
    public function stateRelation()
    {
        return $this->belongsTo(State::class, 'state', 'id');
    }

    //get the country namee and statee namee based on the country and state id
    public function getCountryAndStateAttribute()
    {
        return [
            'country' => $this->countryRelation ? $this->countryRelation->name : null,
            'state' => $this->stateRelation ? $this->stateRelation->name : null,
        ];
    }
}
