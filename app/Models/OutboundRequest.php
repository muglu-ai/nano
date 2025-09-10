<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutboundRequest extends Model
{
    protected $fillable = [
        'endpoint','idempotency_key', 'reg_id', 'payload','status','attempts',
        'response_code','response_body','last_error','responded_at'
    ];

    protected $casts = [
        'payload' => 'array',
        'responded_at' => 'datetime',
    ];
}
