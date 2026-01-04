<?php
// app/Filament/Widgets/LoanStatusDistribution.php

namespace App\Filament\Widgets;

use App\Models\Loan;
use App\Enums\LoanStatus;
use Filament\Widgets\ChartWidget;

class LoanStatusDistribution extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Status Peminjaman';

    protected static ?int $sort = 6;

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $statusCounts = [
            'active' => Loan::where('status', LoanStatus::ACTIVE)->count(),
            'returned' => Loan::where('status', LoanStatus::RETURNED)->count(),
            'overdue' => Loan::where('status', LoanStatus::ACTIVE)
                ->where('due_date', '<', now())
                ->count(),
            'cancelled' => Loan::where('status', LoanStatus::CANCELLED)->count(),
        ];

        return [
            'datasets' => [
                [
                    'data' => array_values($statusCounts),
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',   // Active - Blue
                        'rgba(16, 185, 129, 0.8)',   // Returned - Green
                        'rgba(239, 68, 68, 0.8)',    // Overdue - Red
                        'rgba(156, 163, 175, 0.8)',  // Cancelled - Gray
                    ],
                ],
            ],
            'labels' => ['Aktif', 'Dikembalikan', 'Terlambat', 'Dibatalkan'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
