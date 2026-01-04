<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockMovement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_id',
        'movement_type',
        'quantity',
        'from_location_id',
        'to_location_id',
        'reference_number',
        'reason',
        'performed_by',
        'performed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'item_id' => 'integer',
            'from_location_id' => 'integer',
            'to_location_id' => 'integer',
            'performed_by' => 'integer',
            'performed_at' => 'timestamp',
        ];
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function fromLocation()
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function toLocation()
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
