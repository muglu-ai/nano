<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use App\Constants\FormConstants;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'del_ticket';
    protected $fillable = [
        'ticket_type',
        'nationality',
        'early_bird_date',
        'early_bird_price',
        'normal_price',
        'status',
    ];
}