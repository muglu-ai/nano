<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplimentaryDelegate extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'exhibition_participant_id',
        'title',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'mobile',
        'job_title',
        'organisation_name',
        'token',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'buisness_nature',
        'products',
        'id_type',
        'id_no',
        'profile_pic',
        'unique_id',
        'regId',
        'api_sent',
        'api_response',
        'api_data',
        'emailSent',
    ];

    public function exhibitionParticipant()
    {
        return $this->belongsTo(ExhibitionParticipant::class);
    }

    //find the state and country relation
    public function stateRelation()
    {
        return $this->belongsTo(State::class, 'state');
    }

    public function countryRelation()
    {
        return $this->belongsTo(Country::class, 'country');
    }
}
