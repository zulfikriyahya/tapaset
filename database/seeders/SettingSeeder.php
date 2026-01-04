<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'loan_duration_days',
                'value' => '7',
                'type' => 'integer',
                'group' => 'loan',
                'description' => 'Durasi peminjaman default (hari)',
                'is_public' => true,
            ],
            [
                'key' => 'penalty_per_day',
                'value' => '5000',
                'type' => 'integer',
                'group' => 'penalty',
                'description' => 'Denda per hari keterlambatan (Rupiah)',
                'is_public' => true,
            ],
            [
                'key' => 'max_loan_items_student',
                'value' => '3',
                'type' => 'integer',
                'group' => 'loan',
                'description' => 'Maksimal item yang bisa dipinjam siswa',
                'is_public' => true,
            ],
            [
                'key' => 'max_loan_items_teacher',
                'value' => '5',
                'type' => 'integer',
                'group' => 'loan',
                'description' => 'Maksimal item yang bisa dipinjam guru',
                'is_public' => true,
            ],
            [
                'key' => 'overdue_notification_days',
                'value' => '1',
                'type' => 'integer',
                'group' => 'notification',
                'description' => 'Kirim notifikasi H-x sebelum jatuh tempo',
                'is_public' => false,
            ],
            [
                'key' => 'auto_suspend_after_days',
                'value' => '7',
                'type' => 'integer',
                'group' => 'loan',
                'description' => 'Auto suspend user setelah x hari terlambat',
                'is_public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
