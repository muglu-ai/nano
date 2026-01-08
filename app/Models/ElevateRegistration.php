<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ElevateRegistration extends Model
{
    use SoftDeletes;

    protected $table = 'elevateregistration';

    protected $fillable = [
        'company_name',
        'address',
        'country',
        'state',
        'city',
        'postal_code',
        'elevate_application_call_names',
        'elevate_2025_id',
        'attendance',
        'attendance_reason',
    ];

    protected $casts = [
        'elevate_application_call_names' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get all attendees for this registration
     */
    public function attendees(): HasMany
    {
        return $this->hasMany(ElevateAttendee::class, 'registration_id');
    }
}
