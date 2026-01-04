<?php

namespace App\Enums;

enum UserRole: string
{
    case STUDENT = 'student';
    case TEACHER = 'teacher';
    case STAFF = 'staff';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::STUDENT => 'Siswa',
            self::TEACHER => 'Guru',
            self::STAFF => 'Staff',
            self::ADMIN => 'Admin',
        };
    }
}
