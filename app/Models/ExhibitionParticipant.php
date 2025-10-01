<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Application;
use App\Models\Ticket;

class ExhibitionParticipant extends Model
{
    //
    use HasFactory;

    protected $fillable = ['application_id', 'stall_manning_count', 'complimentary_delegate_count', 'coExhibitor_id', 'ticketAllocation', ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function stallManning()
    {
        return $this->hasMany(StallManning::class);
    }

    public function complimentaryDelegates()
    {
        return $this->hasMany(ComplimentaryDelegate::class);
    }

    // make a function where it passes the ticketAllocation with ticketname and count of it > 0
    //
    public function tickets()
    {
        $tickets = [];
        $allocations = json_decode($this->ticketAllocation, true) ?? [];
        foreach ($allocations as $ticketId => $count) {
            if ($count > 0) {
                $ticket = Ticket::find($ticketId);
                if ($ticket) {
                    $tickets[] = [
                        'name' => $ticket->ticket_type,
                        'count' => $count,
                    ];
                }
            }
        }
        return $tickets;
    }
    //now even coexhibitors can be allocated the bagde count 
    public function coExhibitor()
    {
        return $this->belongsTo(CoExhibitor::class, 'coExhibitor_id');
    }
}
