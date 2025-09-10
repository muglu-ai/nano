<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendeeLog extends Model
{
    protected $table = 'attendee_logs';

    protected $fillable = [
        'attendee_id',
        'name',
        'email',
        'data',
        'deleted_at',
    ];

    // If you want Laravel to manage created_at and updated_at automatically
    public $timestamps = true;

    // If you want to cast the data column as an array automatically
    protected $casts = [
        'data' => 'array',
        'deleted_at' => 'datetime',
    ];
}