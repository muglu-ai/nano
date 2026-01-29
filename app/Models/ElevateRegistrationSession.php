<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElevateRegistrationSession extends Model
{
    protected $table = 'elevate_registration_sessions';

    protected $fillable = [
        'session_id',
        'form_data',
        'progress_percentage',
        'is_abandoned',
        'abandoned_at',
        'expires_at',
        'converted_at',
        'converted_to_registration_id',
    ];

    protected $casts = [
        'form_data' => 'array',
        'is_abandoned' => 'boolean',
        'progress_percentage' => 'integer',
        'expires_at' => 'datetime',
        'abandoned_at' => 'datetime',
        'converted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope to get active (non-abandoned, non-expired) sessions
     */
    public function scopeActive($query)
    {
        return $query->where('is_abandoned', false)
                    ->where(function($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    /**
     * Scope to get sessions by session ID
     */
    public function scopeBySession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Get the registration this session was converted to
     */
    public function registration()
    {
        return $this->belongsTo(ElevateRegistration::class, 'converted_to_registration_id');
    }
}
