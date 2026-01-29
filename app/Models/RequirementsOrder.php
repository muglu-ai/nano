<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequirementsOrder extends Model
{
    use HasFactory;

    protected $fillable = ['application_id', 'invoice_id', 'user_id', 'order_status', 'co_exhibitor_id', 'delivery_status', 'remarks', 'delete'];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function items()
    {
        return $this->hasMany(RequirementOrderItem::class);
    }

    //has payments relationship
    public function payments()
    {
        return $this->hasOne(Payment::class);
    }




    public function orderItems()
    {
        return $this->hasMany(RequirementOrderItem::class, 'requirements_order_id');
    }
}
