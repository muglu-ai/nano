<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExhibitorInfo extends Model
{
    protected $table = 'exhibitors_info';

    protected $fillable = [
        'application_id',
        'contact_person',
        'email',
        'phone',
        'telPhone',
        'fascia_name',
        'company_name',
        'sector',
        'country',
        'state',
        'city',
        'zip_code',
        'logo',
        'description',
        'linkedin',
        'instagram',
        'facebook',
        'youtube',
        'website',
        'address',
        'designation',
        'submission_status',
        'category',
        'api_status',
        'api_message',

    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function products()
    {
        return $this->hasMany(ExhibitorProduct::class, 'exhibitor_id');
    }

    public function pressReleases()
    {
        return $this->hasMany(ExhibitorPressRelease::class, 'exhibitor_id');
    }
}
