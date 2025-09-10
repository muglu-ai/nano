<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationProduct extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'application_id',
        'product_category_id',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class);
    }
}
