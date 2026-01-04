<?php
// app/Models/StockMovement.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockMovement extends Model
{
    use HasFactory;

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

    protected $casts = [
        'performed_at' => 'datetime',
    ];

    // Relationships
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function fromLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function toLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
