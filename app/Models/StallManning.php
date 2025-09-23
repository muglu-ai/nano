<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StallManning extends Model
{
    //

    use HasFactory;

    protected $table = 'stall_manning';

    protected $fillable = [
        'exhibition_participant_id',
        'unique_id',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'mobile',
        'job_title',
        'organisation_name',
        'ticketType',
        'token',
        'id_type',
        'id_no',
        'confirmedCategory',
        'pinNo',
        'api_sent',
        'api_response',
        'api_data',
        'emailSent',
    ];

    protected $appends = ['company_name', 'full_name'];

    public function exhibitionParticipant()
    {
        return $this->belongsTo(ExhibitionParticipant::class, 'exhibition_participant_id');
    }

    public function application()
    {
        return $this->hasOneThrough(
            Application::class,
            ExhibitionParticipant::class,
            'id',
            'id',
            'exhibition_participant_id',
            'application_id'
        );
    }

    public function coExhibitor()
    {
        return $this->hasOneThrough(
            CoExhibitor::class,
            ExhibitionParticipant::class,
            'id',
            'id',
            'exhibition_participant_id',
            'coExhibitor_id'
        );
    }

    public function getCompanyNameAttribute()
    {
        // First check the organization name
        if (!empty($this->organisation_name)) {
            return $this->organisation_name;
        }

        // Then check through exhibition participant for application
        if ($this->exhibitionParticipant && $this->exhibitionParticipant->application) {
            return $this->exhibitionParticipant->application->company_name;
        }

        // Finally check through exhibition participant for co-exhibitor
        if ($this->exhibitionParticipant && $this->exhibitionParticipant->coExhibitor) {
            return $this->exhibitionParticipant->coExhibitor->co_exhibitor_name;
        }

        return '';
    }

    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }


    
}

class AttendeeLog extends Model
    {
        protected $table = 'attendee_logs';
        protected $guarded = []; // allow mass insert
    }
