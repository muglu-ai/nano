<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delegate extends Model
{
    //
    protected $fillable = [
        'first_name', 'last_name', 'email', 'mobile', 'job_title', 'organisation_name', 'application_id'
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
