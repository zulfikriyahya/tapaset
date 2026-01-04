<?php
// app/Filament/Widgets/PopularItemsChart.php

namespace App\Filament\Widgets;

use App\Models\Item;
use App\Models\Loan;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PopularItemsChart extends ChartWidget
{
    protected static ?string $heading = 'Top 10 Barang Paling Sering Dipinjam';

    protected static ?int $sort = 5;

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $popularItems = Loan::select('item_id', DB::raw('COUNT(*) as loan_count'))
            ->with('item')
            ->groupBy('item_id')
            ->orderByDesc('loan_count')
            ->limit(10)
            ->get();

        $labels = [];
        $data = [];

        foreach ($popularItems as $item) {
            $labels[] = $item->item->name ?? 'Unknown';
            $data[] = $item->loan_count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Peminjaman',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(14, 165, 233, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(251, 146, 60, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
