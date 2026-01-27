<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosterAuthor extends Model
{
    protected $fillable = [
        'poster_registration_id',
        'tin_no',
        'token',
        'author_index',
        'title',
        'first_name',
        'last_name',
        'designation',
        'email',
        'mobile',
        'cv_path',
        'cv_original_name',
        'is_lead_author',
        'is_presenter',
        'will_attend',
        'country_id',
        'state_id',
        'city',
        'postal_code',
        'institution',
        'affiliation_city',
        'affiliation_country_id',
    ];

    protected $casts = [
        'is_lead_author' => 'boolean',
        'is_presenter' => 'boolean',
        'will_attend' => 'boolean',
    ];

    /**
     * Get the country for residential address
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /**
     * Get the state for residential address
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    /**
     * Get the country for affiliation address
     */
    public function affiliationCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'affiliation_country_id');
    }

    /**
     * Get the poster registration this author belongs to
     */
    public function posterRegistration(): BelongsTo
    {
        return $this->belongsTo(PosterRegistration::class, 'poster_registration_id');
    }

    /**
     * Get the full name of the author
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
