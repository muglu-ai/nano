<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeletedEventContact extends Model
{
    use HasFactory;

    protected $table = 'event_contacts_delete';

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
