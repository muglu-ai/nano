<?

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MeetingRoomSlot extends Model
{
    use HasFactory;

    protected $table = 'meeting_room_slots';

    protected $fillable = [
        'room_type_id',
        'slot_name',
        'start_time',
        'end_time'
    ];

    public function roomType()
    {
        return $this->belongsTo(MeetingRoomType::class, 'room_type_id');
    }

    public function bookings()
    {
        return $this->hasMany(MeetingRoomBooking::class, 'slot_id');
    }
}
