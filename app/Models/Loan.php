<?php

namespace App\Models;

use App\Enums\LoanStatus;
use App\Enums\ItemCondition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
            'status' => LoanStatus::class,
            'returned_condition' => ItemCondition::class,
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function returnedBy()
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
