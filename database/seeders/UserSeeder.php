<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'identity_number' => 'ADM001',
            'phone' => '081234567890',
            'role' => UserRole::ADMIN,
            'department' => 'IT',
            'max_loan_items' => 10,
            'loan_duration_days' => 14,
        ]);

        // Sample Student
        User::create([
            'name' => 'John Doe',
            'email' => 'student@example.com',
            'password' => Hash::make('password'),
            'identity_number' => 'STD001',
            'phone' => '081234567891',
            'role' => UserRole::STUDENT,
            'department' => 'Teknik Informatika',
            'class' => 'XII RPL 1',
            'max_loan_items' => 3,
            'loan_duration_days' => 7,
        ]);

        // Sample Teacher
        User::create([
            'name' => 'Jane Smith',
            'email' => 'teacher@example.com',
            'password' => Hash::make('password'),
            'identity_number' => 'TCH001',
            'phone' => '081234567892',
            'role' => UserRole::TEACHER,
            'department' => 'Matematika',
            'max_loan_items' => 5,
            'loan_duration_days' => 14,
        ]);
    }
}
