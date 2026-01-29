<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnquiryInterest extends Model
{
    protected $fillable = [
        'enquiry_id', 'interest_type', 'interest_other_detail'
    ];

    /**
     * Get the enquiry that this interest belongs to
     */
    public function enquiry(): BelongsTo
    {
        return $this->belongsTo(Enquiry::class);
    }

    /**
     * Interest type constants
     */
    public const TYPE_DELEGATE = 'delegate';
    public const TYPE_STARTUP_POD = 'startup_pod';
    public const TYPE_SPEAKING = 'speaking';
    public const TYPE_EXHIBITION = 'exhibition';
    public const TYPE_SPONSORING = 'sponsoring';
    public const TYPE_B2B = 'b2b';
    public const TYPE_VISITOR = 'visitor';
    public const TYPE_POSTER = 'poster';
    public const TYPE_OTHER = 'other';

    /**
     * Get all interest types
     */
    public static function getInterestTypes(): array
    {
        return [
            self::TYPE_DELEGATE => 'Attending as Delegate',
            self::TYPE_STARTUP_POD => 'Startup / POD',
            self::TYPE_SPEAKING => 'Speaking Opportunity',
            self::TYPE_EXHIBITION => 'Exhibition',
            self::TYPE_SPONSORING => 'Sponsoring',
            self::TYPE_B2B => 'B2B Meetings',
            self::TYPE_VISITOR => 'Visitor',
            self::TYPE_POSTER => 'Poster',
            self::TYPE_OTHER => 'Other',
        ];
    }
}
