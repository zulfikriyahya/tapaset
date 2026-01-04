<?php

namespace App\Models;

use App\Enums\ItemStatus;
use App\Enums\ItemCondition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'purchase_date' => 'date',
            'price' => 'decimal:2',
            'warranty_expired_at' => 'date',
            'location_id' => 'integer',
            'category_id' => 'integer',
            'condition' => ItemCondition::class,
            'status' => ItemStatus::class,
        ];
    }

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
}
