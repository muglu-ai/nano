<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SponsorItem extends Model
{
    //
    protected $fillable = [
        'name',
        'price',
        'mem_price',
        'no_of_items',
        'deliverables',
        'status',
        'category_id',
        'image_url',
        'quantity_desc',
    ];

    // a user can have many sponsorships
    // public function sponsorships()
    // {
    //     return $this->hasMany(SponsorItem::class);
    // }

    public function sponsorshipItem()
    {
        return $this->belongsTo(SponsorItem::class, 'sponsorship_item_id');
    }

    

    //
}
