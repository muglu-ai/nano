<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplimentaryDelegate2 extends Model
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
        'inauguralConfirmation',
        'inaugural_session',
        'approvedHistory',
    ];

    public function exhibitionParticipant()
    {
        return $this->belongsTo(ExhibitionParticipant::class);
    }
}
