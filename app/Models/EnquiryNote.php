<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnquiryNote extends Model
{
    protected $fillable = [
        'enquiry_id', 'note', 'note_type', 'created_by_user_id', 'created_by_name'
    ];

    /**
     * Get the enquiry that this note belongs to
     */
    public function enquiry(): BelongsTo
    {
        return $this->belongsTo(Enquiry::class);
    }

    /**
     * Get the user who created this note
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
