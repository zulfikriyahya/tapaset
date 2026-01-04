<?php
// app/Filament/Widgets/OverdueLoansChart.php

namespace App\Filament\Widgets;

use App\Models\Loan;
use App\Enums\LoanStatus;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class OverdueLoansChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Peminjaman (30 Hari Terakhir)';

    protected static ?int $sort = 2;

    protected static ?string $pollingInterval = '60s';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // Get last 30 days data
        $data = Loan::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN status = "' . LoanStatus::ACTIVE->value . '" AND due_date < NOW() THEN 1 ELSE 0 END) as overdue')
        )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $totalLoans = [];
        $overdueLoans = [];

        foreach ($data as $item) {
            $labels[] = date('d M', strtotime($item->date));
            $totalLoans[] = $item->total;
            $overdueLoans[] = $item->overdue;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Peminjaman',
                    'data' => $totalLoans,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Terlambat',
                    'data' => $overdueLoans,
                    'borderColor' => 'rgb(239, 68, 68)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
