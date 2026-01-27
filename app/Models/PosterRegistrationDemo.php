<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosterRegistrationDemo extends Model
{
    protected $fillable = [
        'token',
        'tin_no',
        'session_id',
        'sector',
        'currency',
        'poster_category',
        'abstract_title',
        'abstract',
        'extended_abstract_path',
        'extended_abstract_original_name',
        'lead_auth_cv_path',
        'lead_auth_cv_original_name',
        'authors',
        'lead_author_index',
        'presenter_index',
        'presentation_mode',
        'attendee_count',
        'base_amount',
        'gst_amount',
        'processing_fee',
        'total_amount',
        'publication_permission',
        'authors_approval',
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
    ];


}
