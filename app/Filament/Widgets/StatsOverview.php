<?php
// app/Filament/Widgets/StatsOverview.php

namespace App\Filament\Widgets;

use App\Models\Item;
use App\Models\Loan;
use App\Models\User;
use App\Models\RfidCard;
use App\Enums\ItemStatus;
use App\Enums\LoanStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        return Cache::remember(
            'dashboard-stats',
            300,
            function () {
                // Total Items
                $totalItems = Item::count();
                $availableItems = Item::where('status', ItemStatus::AVAILABLE)->count();
                $itemsPercentage = $totalItems > 0 ? round(($availableItems / $totalItems) * 100) : 0;

                // Active Loans
                $activeLoans = Loan::where('status', LoanStatus::ACTIVE)->count();
                $overdueLoans = Loan::where('status', LoanStatus::ACTIVE)
                    ->where('due_date', '<', now())
                    ->count();

                // Loans trend (last 7 days vs previous 7 days)
                $currentWeekLoans = Loan::whereBetween('created_at', [now()->subDays(7), now()])->count();
                $previousWeekLoans = Loan::whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])->count();
                $loansTrend = $previousWeekLoans > 0
                    ? round((($currentWeekLoans - $previousWeekLoans) / $previousWeekLoans) * 100)
                    : 0;

                // Total Users
                $totalUsers = User::count();
                $activeUsers = User::where('is_suspended', false)->count();
                $suspendedUsers = User::where('is_suspended', true)->count();

                // RFID Cards
                $totalCards = RfidCard::count();
                $assignedCards = RfidCard::whereNotNull('user_id')->count();
                $activeCards = RfidCard::where('is_active', true)->count();

                // Unpaid Penalties
                $unpaidPenalties = Loan::where('penalty_amount', '>', 0)
                    ->where('is_paid', false)
                    ->sum('penalty_amount');

                // Low Stock Items
                $lowStockItems = Item::whereColumn('quantity', '<=', 'min_quantity')
                    ->whereNotNull('min_quantity')
                    ->count();

                return [
                    Stat::make('Total Barang', $totalItems)
                        ->description("{$availableItems} tersedia ({$itemsPercentage}%)")
                        ->descriptionIcon('heroicon-m-cube')
                        ->color('success')
                        ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                        ->url(route('filament.admin.resources.items.index')),

                    Stat::make('Peminjaman Aktif', $activeLoans)
                        ->description($overdueLoans > 0 ? "{$overdueLoans} terlambat" : 'Semua tepat waktu')
                        ->descriptionIcon($overdueLoans > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                        ->color($overdueLoans > 0 ? 'danger' : 'info')
                        ->chart([3, 5, 4, 6, 5, 7, 8])
                        ->url(route('filament.admin.resources.loans.index')),

                    Stat::make('Pengguna', $totalUsers)
                        ->description("{$activeUsers} aktif, {$suspendedUsers} suspended")
                        ->descriptionIcon('heroicon-m-users')
                        ->color($suspendedUsers > 0 ? 'warning' : 'success')
                        ->url(route('filament.admin.resources.users.index')),

                    Stat::make('Kartu RFID', $totalCards)
                        ->description("{$assignedCards} ter-assign, {$activeCards} aktif")
                        ->descriptionIcon('heroicon-m-credit-card')
                        ->color('info')
                        ->url(route('filament.admin.resources.rfid-cards.index')),

                    Stat::make('Denda Belum Lunas', 'Rp ' . number_format($unpaidPenalties, 0, ',', '.'))
                        ->description('Total denda keterlambatan')
                        ->descriptionIcon('heroicon-m-currency-dollar')
                        ->color($unpaidPenalties > 0 ? 'danger' : 'success')
                        ->url(route('filament.admin.resources.loans.index', ['tableFilters[unpaid_penalty][value]' => true])),

                    Stat::make('Stok Rendah', $lowStockItems)
                        ->description('Barang perlu restock')
                        ->descriptionIcon('heroicon-m-arrow-trending-down')
                        ->color($lowStockItems > 0 ? 'warning' : 'success')
                        ->url(route('filament.admin.resources.items.index', ['tableFilters[low_stock][value]' => true])),
                ];
            }
        );
    }
}
