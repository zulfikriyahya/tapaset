<?php

namespace App\Enums;

enum LoanStatus: string
{
    case ACTIVE = 'active';
    case RETURNED = 'returned';
    case OVERDUE = 'overdue';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Aktif',
            self::RETURNED => 'Dikembalikan',
            self::OVERDUE => 'Terlambat',
            self::CANCELLED => 'Dibatalkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'info',
            self::RETURNED => 'success',
            self::OVERDUE => 'danger',
            self::CANCELLED => 'secondary',
        };
    }
}
