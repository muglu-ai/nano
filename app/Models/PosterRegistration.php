<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosterRegistration extends Model
{
    protected $fillable = [
        'tin_no',
        'token',
        'sector',
        'currency',
        'poster_category',
        'abstract_title',
        'abstract',
        'extended_abstract_path',
        'extended_abstract_original_name',
        'authors',
        'lead_author_index',
        'presenter_index',
        'lead_author_name',
        'lead_author_email',
        'lead_author_mobile',
        'presentation_mode',
        'attendee_count',
        'base_amount',
        'gst_amount',
        'processing_fee',
        'total_amount',
        'publication_permission',
        'authors_approval',
        'payment_status',
        'payment_method',
        'payment_transaction_id',
        'payment_date',
        'status',
    ];

    protected $casts = [
        'authors' => 'array',
        'publication_permission' => 'boolean',
        'authors_approval' => 'boolean',
        'base_amount' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'processing_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'payment_date' => 'datetime',
    ];
}
