<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'user_id' => 'integer',
            'item_id' => 'integer',
            'loan_date' => 'timestamp',
            'due_date' => 'timestamp',
            'return_date' => 'timestamp',
            'created_by' => 'integer',
            'returned_by' => 'integer',
            'approved_by' => 'integer',
            'penalty_amount' => 'decimal:2',
            'is_paid' => 'boolean',
        ];
    }

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
        return $this->belongsTo(User::class);
    }

    public function returnedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
