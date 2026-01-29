<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminActionLog extends Model
{
    //

    protected $table = 'admin_action_logs';

    // const CREATED_AT = 'timestamp';
    // const UPDATED_AT = null;

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];
    protected $fillable = [
        'user_id',
        'action',
        'timestamp',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
