<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    //now even coexhibitors can be allocated the bagde count 
    public function coExhibitor()
    {
        return $this->belongsTo(CoExhibitor::class, 'coExhibitor_id');
    }
}
