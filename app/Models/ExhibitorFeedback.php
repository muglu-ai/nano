<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExhibitorFeedback extends Model
{
    use HasFactory;

    protected $table = 'exhibitor_feedback';

    protected $fillable = [
        'user_id', // Optional - can be null for public submissions
        'name',
        'email',
        'company_name',
        'phone',
        'event_rating',
        'portal_rating',
        'overall_experience_rating',
        'what_liked_most',
        'what_could_be_improved',
        'additional_comments',
        'would_recommend',
        'event_organization_rating',
        'venue_rating',
        'networking_opportunities_rating',
    ];

    protected $casts = [
        'event_rating' => 'integer',
        'portal_rating' => 'integer',
        'overall_experience_rating' => 'integer',
        'event_organization_rating' => 'integer',
        'venue_rating' => 'integer',
        'networking_opportunities_rating' => 'integer',
    ];

    /**
     * Get the user that submitted the feedback
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

