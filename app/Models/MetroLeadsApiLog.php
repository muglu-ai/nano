<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetroLeadsApiLog extends Model
{
    protected $table = 'metroleads_api_logs';

    protected $fillable = [
        'enquiry_id',
        'request_data',
        'response_data',
        'status',
        'http_code',
        'error_message',
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
    ];

    /**
     * Get the enquiry that triggered this API call
     */
    public function enquiry(): BelongsTo
    {
        return $this->belongsTo(Enquiry::class);
    }
}
