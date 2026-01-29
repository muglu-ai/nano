<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StallManning3 extends Model
{
    //

    use HasFactory;

    protected $table = 'stall_manning';

    protected $fillable = ['exhibition_participant_id', 'first_name', 'last_name', 'email', 'mobile', 'job_title', 'organisation_name', 'id_type', 'id_no'];

    public function exhibitionParticipant()
    {
        return $this->belongsTo(ExhibitionParticipant::class);
    }
}
