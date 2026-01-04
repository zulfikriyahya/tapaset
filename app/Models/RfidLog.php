<?php
// app/Models/RfidLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RfidLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'rfid_card_id',
        'user_id',
        'action',
        'status',
        'item_id',
        'loan_id',
        'location',
        'ip_address',
        'user_agent',
        'response_message',
    ];

    // Relationships
    public function rfidCard(): BelongsTo
    {
        return $this->belongsTo(RfidCard::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }
}
