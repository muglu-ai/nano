<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class BlockedSlot extends Model
{
    protected $fillable = [
        'meeting_room_id',
        'start_time',
        'end_time',
        'date',
    ];

    public function meetingRoom()
    {
        return $this->belongsTo(MeetingRoom::class);
    }
}