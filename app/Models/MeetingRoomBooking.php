<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class MeetingRoomBooking extends Model
{
    use HasFactory;

    protected $table = 'meeting_room_bookings';
    protected $appends = ['booking_time', 'formatted_date', 'status_label'];

    protected $fillable = [
        'application_id',
        'user_id',
        'room_type_id',
        'slot_id',
        'booking_date',
        'is_member',
        'final_price',
        'payment_status',
        'booking_id',
    ];

    public function roomType()
    {
        return $this->belongsTo(MeetingRoomType::class, 'room_type_id');
    }

    public function slot()
    {
        return $this->belongsTo(MeetingRoomSlot::class, 'slot_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class); // assuming default Laravel User
    }

    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id'); // make sure Application model exists
    }


    // Get formatted booking time from slot relationship
    public function getBookingTimeAttribute()
    {
        return $this->slot ? "{$this->slot->start_time} - {$this->slot->end_time}" : '';
    }

    // Get formatted date
    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->booking_date)->format('d M Y');
    }

    // Get status label
    public function getStatusLabelAttribute()
    {
        return match ($this->payment_status) {
            'paid' => 'Confirmed',
            'pending' => 'Pending',
            'cancelled' => 'Cancelled',
            default => 'Unknown'
        };
    }

    // Scope to get all bookings with related data
    public function scopeWithBookingDetails($query)
    {
        return $query->with([
            'roomType:id,room_type,location',
            'slot:id,start_time,end_time',
            'user:id,name,email',
            'application:id,company_name'
        ]);
    }

    // Get all necessary booking information
    public static function getAllBookings()
    {
        return static::withBookingDetails()
            ->select([
                'id',
                'booking_id',
                'booking_date',
                'payment_status',
                'room_type_id',
                'slot_id',
                'user_id',
                'application_id'
            ])
            ->orderBy('booking_date', 'desc')
            ->get();
    }
}
