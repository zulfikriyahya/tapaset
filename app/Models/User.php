<?php
// app/Models/User.php

namespace App\Models;

use App\Enums\UserRole;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'identity_number',
        'phone',
        'role',
        'department',
        'class',
        'is_suspended',
        'suspended_until',
        'suspension_reason',
        'max_loan_items',
        'loan_duration_days',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => UserRole::class,
        'is_suspended' => 'boolean',
        'suspended_until' => 'datetime',
    ];

    // Relationships
    public function rfidCard(): HasOne
    {
        return $this->hasOne(RfidCard::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function createdLoans(): HasMany
    {
        return $this->hasMany(Loan::class, 'created_by');
    }

    public function returnedLoans(): HasMany
    {
        return $this->hasMany(Loan::class, 'returned_by');
    }

    public function approvedLoans(): HasMany
    {
        return $this->hasMany(Loan::class, 'approved_by');
    }

    public function rfidLogs(): HasMany
    {
        return $this->hasMany(RfidLog::class);
    }

    public function createdMaintenanceHistories(): HasMany
    {
        return $this->hasMany(MaintenanceHistory::class, 'created_by');
    }

    public function performedStockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'performed_by');
    }
}
