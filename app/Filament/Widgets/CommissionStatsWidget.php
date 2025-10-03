<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Widget wyświetlający statystyki prowizji platformy.
 *
 * Pokazuje przychody z prowizji (15% od każdej transakcji):
 * - Całkowita prowizja
 * - Prowizja w bieżącym miesiącu
 * - Liczba transakcji
 *
 * @author Claude AI Assistant
 * @since 1.0.0
 */
class CommissionStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        // Całkowita prowizja (ze wszystkich ukończonych płatności)
        $totalCommission = Payment::where('status', 'completed')
            ->sum('commission_amount');

        // Prowizja w bieżącym miesiącu
        $currentMonthCommission = Payment::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('commission_amount');

        // Prowizja w poprzednim miesiącu (do porównania)
        $previousMonthCommission = Payment::where('status', 'completed')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('commission_amount');

        // Oblicz trend
        $commissionTrend = $previousMonthCommission > 0
            ? round((($currentMonthCommission - $previousMonthCommission) / $previousMonthCommission) * 100, 1)
            : 0;

        // Liczba transakcji w miesiącu
        $monthlyTransactions = Payment::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Średnia wartość transakcji
        $averageTransaction = $monthlyTransactions > 0
            ? $currentMonthCommission / $monthlyTransactions
            : 0;

        return [
            Stat::make('Całkowita prowizja', number_format($totalCommission, 2, ',', ' ').' zł')
                ->description('15% od wszystkich transakcji')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Prowizja w miesiącu', number_format($currentMonthCommission, 2, ',', ' ').' zł')
                ->description(($commissionTrend >= 0 ? '+' : '').$commissionTrend.'% vs poprzedni miesiąc')
                ->descriptionIcon($commissionTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($commissionTrend >= 0 ? 'success' : 'danger'),

            Stat::make('Transakcje w miesiącu', $monthlyTransactions)
                ->description('Średnia: '.number_format($averageTransaction, 2, ',', ' ').' zł')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('info'),
        ];
    }
}
