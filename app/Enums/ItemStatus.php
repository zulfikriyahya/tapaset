<?php

namespace App\Enums;

enum ItemStatus: string
{
    case AVAILABLE = 'available';
    case LOANED = 'loaned';
    case MAINTENANCE = 'maintenance';
    case RETIRED = 'retired';

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Tersedia',
            self::LOANED => 'Dipinjam',
            self::MAINTENANCE => 'Maintenance',
            self::RETIRED => 'Tidak Aktif',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::AVAILABLE => 'success',
            self::LOANED => 'warning',
            self::MAINTENANCE => 'info',
            self::RETIRED => 'danger',
        };
    }
}
