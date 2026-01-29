<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'salutation',
        'first_name',
        'last_name',
        'job_title',
        'email',
        'contact_number',
        'secondary_email',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

}
