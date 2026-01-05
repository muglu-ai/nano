<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnquiryFollowup extends Model
{
    protected $fillable = [
        'enquiry_id', 'followup_type', 'followup_status', 'followup_comment',
        'followup_date', 'followup_time', 'followup_datetime',
        'assigned_to_user_id', 'assigned_to_name', 'prospect_level'
    ];

    protected $casts = [
        'followup_date' => 'date',
        'followup_time' => 'datetime',
        'followup_datetime' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the enquiry that this followup belongs to
     */
    public function enquiry(): BelongsTo
    {
        return $this->belongsTo(Enquiry::class);
    }

    /**
     * Get the user assigned to this followup
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }
}
