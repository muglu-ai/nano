<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoExhibitor extends Model
{
    //

    use HasFactory;

    protected $fillable = [
        'application_id',
        'co_exhibitor_name',
        'contact_person',
        'email',
        'phone',
        'status',
        'job_title',
        'proof_document',
        'stall_size',
        'booth_number',
        'co_exhibitor_id',
        'approved_At',
        'purchase_allowed',
        'pavilion_name',
        'stall_size',
        'booth_number',
        'purchase_allowed',
        'address1',
        'city',
        'state',
        'zip',
        'country',


    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
