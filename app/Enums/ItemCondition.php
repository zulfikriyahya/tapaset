<?php

namespace App\Enums;

enum ItemCondition: string
{
    case GOOD = 'good';
    case FAIR = 'fair';
    case DAMAGED = 'damaged';

    public function label(): string
    {
        return match ($this) {
            self::GOOD => 'Baik',
            self::FAIR => 'Cukup',
            self::DAMAGED => 'Rusak',
        };
    }
}
