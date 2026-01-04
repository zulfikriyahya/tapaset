<?php
// app/Models/MaintenanceHistory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaintenanceHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'item_id',
        'maintenance_type',
        'description',
        'cost',
        'performed_by',
        'performed_at',
        'completed_at',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'performed_at' => 'datetime',
        'completed_at' => 'datetime',
        'cost' => 'decimal:2',
    ];

    // Relationships
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
