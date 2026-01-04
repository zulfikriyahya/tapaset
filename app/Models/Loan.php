<?php

// app/Models/Loan.php

namespace App\Models;

use App\Enums\ItemCondition;
use App\Enums\LoanStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'loan_number',
        'user_id',
        'item_id',
        'loan_date',
        'due_date',
        'return_date',
        'returned_condition',
        'status',
        'loan_notes',
        'return_notes',
        'created_by',
        'returned_by',
        'approved_by',
        'penalty_amount',
        'is_paid',
    ];

    protected $casts = [
        'status' => LoanStatus::class,
        'returned_condition' => ItemCondition::class,
        'loan_date' => 'datetime',
        'due_date' => 'datetime',
        'return_date' => 'datetime',
        'penalty_amount' => 'decimal:2',
        'is_paid' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function returnedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rfidLogs(): HasMany
    {
        return $this->hasMany(RfidLog::class);
    }

    // Helper Methods
    public function isOverdue(): bool
    {
        return $this->status === LoanStatus::ACTIVE && $this->due_date < now();
    }

    public function getDaysLateAttribute(): int
    {
        if (! $this->isOverdue()) {
            return 0;
        }

        return now()->diffInDays($this->due_date);
    }
}
