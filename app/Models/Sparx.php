<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Sparx extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sparxes';

    /**
     * The primary key associated with the table (still 'id')
     * registered_id is just another unique column, not the PK
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'registered_id',
        'uuid',
        'event_id',
        'event_year',

        'name',
        'designation',
        'organization',

        'email',
        'phone_country_code',
        'phone_number',
        'phone_full',

        'address',
        'city',
        'state',
        'country',
        'postal_code',

        'startup_idea_name',
        'website',

        'sector',

        'idea_description',
        'products',
        'key_successes',

        'potential_market_size',
        'company_size_employees',

        'is_registered',
        'registration_date',

        'consent_given',
        'ip_address',
        'user_agent',

        'status',
    ];

    protected $casts = [
        'is_registered'          => 'boolean',
        'consent_given'          => 'boolean',
        'registration_date'      => 'date:Y-m-d',
        'company_size_employees' => 'integer',
        'deleted_at'             => 'datetime',
        'created_at'             => 'datetime',
        'updated_at'             => 'datetime',
    ];

    protected $hidden = [
        'ip_address',
        'user_agent',
        // 'registered_id',     // ← decide whether you want to expose this
    ];

    /**
     * Get the event that this enquiry belongs to
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Events::class, 'event_id');
    }


    // ------------------------------------------------------------------------
    //  Optional: UUID as route key (if you prefer uuid in URLs instead of id)
    // ------------------------------------------------------------------------

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });

        static::created(function ($model) {
            if (empty($model->registered_id)) {
                $model->registered_id = $model->id;
                $model->saveQuietly();
            }
        });
    }

    public function getFormattedRegistrationIdAttribute(): ?string
    {
        if (!$this->registered_id || !$this->event_year) {
            return null;
        }
        return "NANO-{$this->event_year}-" . str_pad($this->registered_id, 4, '0', STR_PAD_LEFT);
    }

}