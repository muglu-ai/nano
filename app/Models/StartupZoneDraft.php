<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class StartupZoneDraft extends Model
{
    use HasFactory;

    protected $table = 'startup_zone_drafts';

    protected $fillable = [
        'session_id',
        'uuid',
        'stall_category',
        'interested_sqm',
        'company_name',
        'certificate_path',
        'how_old_startup',
        'address',
        'city_id',
        'state_id',
        'postal_code',
        'country_id',
        'landline',
        'website',
        'company_email',
        'gst_compliance',
        'gst_no',
        'pan_no',
        'sector_id',
        'subSector',
        'type_of_business',
        'promocode',
        'assoc_mem',
        'RegSource',
        'contact_data',
        'billing_data',
        'exhibitor_data',
        'payment_mode',
        'application_type',
        'event_id',
        'user_id',
        'last_updated_field',
        'progress_percentage',
        'is_abandoned',
        'abandoned_at',
        'expires_at',
        'converted_to_application_id',
        'converted_at',
    ];

    protected $casts = [
        'contact_data' => 'array',
        'billing_data' => 'array',
        'exhibitor_data' => 'array',
        'gst_compliance' => 'boolean',
        'is_abandoned' => 'boolean',
        'how_old_startup' => 'integer',
        'progress_percentage' => 'integer',
        'expires_at' => 'datetime',
        'abandoned_at' => 'datetime',
        'converted_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    // Accessors & Mutators for encrypted fields
    public function getGstNoAttribute($value)
    {
        if ($value) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    public function setGstNoAttribute($value)
    {
        if ($value) {
            $this->attributes['gst_no'] = Crypt::encryptString($value);
        } else {
            $this->attributes['gst_no'] = null;
        }
    }

    public function getPanNoAttribute($value)
    {
        if ($value) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    public function setPanNoAttribute($value)
    {
        if ($value) {
            $this->attributes['pan_no'] = Crypt::encryptString($value);
        } else {
            $this->attributes['pan_no'] = null;
        }
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_abandoned', false)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeAbandoned($query)
    {
        return $query->where('is_abandoned', true);
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    public function scopeBySession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }
}
