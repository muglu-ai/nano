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
        'status',
        'cancelled_at',
        'cancelled_by',
        'id_type',
        'id_no',
        'confirmedCategory',
        'pinNo',
        'api_sent',
        'api_response',
        'api_data',
        'emailSent',
    ];

    protected $casts = [
        'status' => 'string',
        'cancelled_at' => 'datetime',
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

    /**
     * Get the ticket type for this invitation
     */
    public function ticketType()
    {
        return $this->belongsTo(\App\Models\Ticket\TicketType::class, 'ticketType', 'id');
    }

    /**
     * Get the user who cancelled this invitation
     */
    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * Check if invitation is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Cancel the invitation
     * 
     * @param int|null $userId User ID who is cancelling
     * @return bool
     */
    public function cancel(?int $userId = null): bool
    {
        if ($this->isCancelled()) {
            return true; // Already cancelled
        }

        return $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancelled_by' => $userId,
        ]);
    }
}

class AttendeeLog extends Model
    {
        protected $table = 'attendee_logs';
        protected $guarded = []; // allow mass insert
    }
