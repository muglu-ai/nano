<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeetingRoom extends Model
{
    use HasFactory;

    protected $table = 'meeting_room_types';

    protected $fillable = [
        'room_type',
        'capacity',
        'size_sqm',
        'qty',
        'location',
        'equipment',
        'fnb',
        'member_price',
        'non_member_price',
        'currency',
        'notes'
    ];

    public function slots()
    {
        return $this->hasMany(MeetingRoomSlot::class, 'room_type_id');
    }

    public function bookings()
    {
        return $this->hasMany(MeetingRoomBooking::class, 'room_type_id');
    }
}
