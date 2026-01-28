<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ticket\TicketType;

class ComplimentaryDelegate extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'exhibition_participant_id',
        'ticketType',
        'title',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'mobile',
        'job_title',
        'organisation_name',
        'token',
        'status',
        'cancelled_at',
        'cancelled_by',
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

    public function exhibitionParticipant()
    {
        return $this->belongsTo(ExhibitionParticipant::class);
    }

    /**
     * Get the ticket type for this invitation
     */
    public function ticketType()
    {
        return $this->belongsTo(TicketType::class, 'ticketType', 'id');
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
