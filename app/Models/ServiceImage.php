namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'path',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    // Get the full URL for the image
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }
} 