<?

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExhibitorPressRelease extends Model
{
    protected $fillable = [
        'exhibitor_id',
        'title',
        'file',
        'summary',
        'link',
    ];

    public function exhibitor()
    {
        return $this->belongsTo(ExhibitorInfo::class, 'exhibitor_id');
    }
}
