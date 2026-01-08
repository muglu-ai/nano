<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElevateAttendee extends Model
{
    use SoftDeletes;

    protected $table = 'elevateattendees';

    protected $fillable = [
        'registration_id',
        'salutation',
        'first_name',
        'last_name',
        'job_title',
        'email',
        'phone_number',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the registration that this attendee belongs to
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(ElevateRegistration::class, 'registration_id');
    }
}
