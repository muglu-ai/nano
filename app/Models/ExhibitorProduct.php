<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExhibitorProduct extends Model
{
    protected $fillable = [
        
        'application_id',
        'exhibitor_id',
        'product_name',
        'product_image',
        'description'
    ];

    public function exhibitor()
    {
        return $this->belongsTo(ExhibitorInfo::class, 'exhibitor_id');
    }

    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }
}
