<?php

// app/Models/Item.php

namespace App\Models;

use App\Enums\ItemCondition;
use App\Enums\ItemStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'item_code',
        'serial_number',
        'description',
        'purchase_date',
        'price',
        'warranty_expired_at',
        'condition',
        'status',
        'quantity',
        'min_quantity',
        'location_id',
        'category_id',
        'image',
    ];

    protected $casts = [
        'condition' => ItemCondition::class,
        'status' => ItemStatus::class,
        'purchase_date' => 'date',
        'warranty_expired_at' => 'date',
        'price' => 'decimal:2',
    ];

    // Relationships
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function maintenanceHistories(): HasMany
    {
        return $this->hasMany(MaintenanceHistory::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function rfidLogs(): HasMany
    {
        return $this->hasMany(RfidLog::class);
    }

    // Helper Methods
    public function getNameWithCodeAttribute(): string
    {
        return "{$this->name} ({$this->item_code})";
    }

    public function isLowStock(): bool
    {
        return $this->min_quantity && $this->quantity <= $this->min_quantity;
    }

    public function isWarrantyExpired(): bool
    {
        return $this->warranty_expired_at && $this->warranty_expired_at->isPast();
    }
}
