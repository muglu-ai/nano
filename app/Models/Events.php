<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    //
    protected $table = 'events';
    protected $fillable = [
        'event_year',
        'event_name',
        'event_date',
        'event_location',
        'event_description',
        'event_image',
        'start_date',
        'end_date',
        'slug',
        'status',
    ];
}
