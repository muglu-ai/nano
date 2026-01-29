<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadRetrievalUser extends Model
{
    protected $table = 'lead_retrieval_user';
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'mobile',
        'designation',
        'company_name',
        'registered_at',
    ];
    public $timestamps = false;

    // Relationship to User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
