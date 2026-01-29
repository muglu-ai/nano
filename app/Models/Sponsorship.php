<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sponsorship extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sponsorship_item',
        'price',
        'status',
        'invoice_id',
        'sponsorship_item_id',
        'sponsorship_item_count',
        'application_id',
        'submitted_date',
        'approval_date',
        'sponsorship_id',
        'sponsorship_item_id',
        'sponsorship_item_count'


    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function sponsorshipItem()
    {
        return $this->belongsTo(SponsorItem::class, 'sponsorship_item_id');
    }

    public function sponsorItems()
    {
        return $this->hasMany(SponsorItem::class, 'sponsorship_id', 'id');
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function applications()
    {
        return $this->belongsTo(Application::class, 'application_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(SponsorCategory::class);
    }
}
