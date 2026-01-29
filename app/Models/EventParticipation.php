<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventParticipation extends Model
{
    //
    protected $fillable = [
        'application_id',
        'participation_type',
        'region',
        'previous_participation',
        'stall_categories',
        'interested_sqm',
        'product_groups',
        'sectors',
        'terms_accepted',
    ];

    protected $casts = [
        'stall_categories' => 'array',
        'product_groups' => 'array',
        'sectors' => 'array',
        'previous_participation' => 'boolean',
        'terms_accepted' => 'boolean',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function getSectorsAttribute($value)
    {
        return json_decode($value);
    }

    public function getProductGroupsAttribute($value)
    {
        return json_decode($value);
    }
}
