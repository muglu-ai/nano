<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraRequirement extends Model {
    use HasFactory;

    protected $fillable = [
        'item_name',
        'days',
        'price_for_expo',
        'image',
        'available_quantity',
        'status'
    ];
}
