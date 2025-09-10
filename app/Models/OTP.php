<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OTP extends Model
{
    use HasFactory;

    protected $table = 'otps';

    protected $fillable = [
        'identifier',
        'otp',
        'verified',
        'expires_at',
        'created_at',
        'updated_at',
    ];
}
