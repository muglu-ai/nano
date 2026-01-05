<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enquiry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'event_id', 'event_year', 'title', 'first_name', 'last_name',
        'full_name', 'organisation', 'designation', 'sector',
        'email', 'phone_country_code', 'phone_number', 'phone_full',
        'city', 'state', 'country', 'postal_code', 'address',
        'comments', 'referral_source', 'source_url', 'ip_address',
        'user_agent', 'status', 'prospect_level', 'status_comment',
        'assigned_to_user_id', 'assigned_to_name'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the event that this enquiry belongs to
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Events::class, 'event_id');
    }

    /**
     * Get the user assigned to this enquiry
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    /**
     * Get all interests for this enquiry
     */
    public function interests(): HasMany
    {
        return $this->hasMany(EnquiryInterest::class);
    }

    /**
     * Get all followups for this enquiry
     */
    public function followups(): HasMany
    {
        return $this->hasMany(EnquiryFollowup::class);
    }

    /**
     * Get all notes for this enquiry
     */
    public function notes(): HasMany
    {
        return $this->hasMany(EnquiryNote::class);
    }

    /**
     * Get interest types as array
     */
    public function getInterestTypesAttribute(): array
    {
        return $this->interests->pluck('interest_type')->toArray();
    }
}
