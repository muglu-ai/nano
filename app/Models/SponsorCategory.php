<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SponsorCategory extends Model
{
    protected $table = 'sponsor_categories';

    protected $fillable = [
        'name',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // public function items()
    // {
    //     return $this->hasMany(SponsorItem::class, 'category_id', 'id');
    // }

    public function items()
{
    return $this->hasMany(SponsorItem::class, 'category_id');
}


}
