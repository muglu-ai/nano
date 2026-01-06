<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisaClearanceRequest extends Model
{
    use SoftDeletes;

    protected $table = 'visa_clearance_requests';

    protected $fillable = [
        'event_id', 'event_year',
        'organisation_name', 'designation', 'passport_name', 'father_husband_name',
        'dob', 'place_of_birth', 'nationality',
        'passport_number', 'passport_issue_date', 'passport_issue_place', 'passport_expiry_date',
        'entry_date_india', 'exit_date_india',
        'phone_country_code', 'phone_number', 'email',
        'address_line1', 'address_line2', 'city', 'state', 'country', 'postal_code',
        'source_url', 'ip_address', 'user_agent',
        'status', 'status_comment',
        'assigned_to_user_id', 'assigned_to_name'
    ];

    protected $casts = [
        'dob' => 'date',
        'passport_issue_date' => 'date',
        'passport_expiry_date' => 'date',
        'entry_date_india' => 'date',
        'exit_date_india' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the event that this visa clearance request belongs to
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Events::class, 'event_id');
    }

    /**
     * Get the user assigned to this visa clearance request
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }
}
