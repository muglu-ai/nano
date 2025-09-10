<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequirementOrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['requirements_order_id', 'requirement_id', 'quantity', 'unit_price'];

    public function requirementsOrder()
    {
        return $this->belongsTo(RequirementsOrder::class);
    }

    public function requirements()
    {
        return $this->belongsTo(ExtraRequirement::class);
    }

    public function requirement()
    {
        return $this->belongsTo(ExtraRequirement::class, 'requirement_id');
    }
}

