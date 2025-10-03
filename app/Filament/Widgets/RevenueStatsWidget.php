<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Widget wyświetlający statystyki przychodów.
 *
 * Pokazuje kluczowe metryki finansowe:
 * - Całkowite przychody
 * - Przychody w bieżącym miesiącu
 * - Przeterminowane faktury
 * - Oczekujące płatności
 *
 * @author Claude AI Assistant
 * @since 1.0.0
 */
class RevenueStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Całkowite przychody (faktury opłacone)
        $totalRevenue = Invoice::where('status', 'paid')
            ->sum('gross_amount');

        // Przychody w bieżącym miesiącu
        $currentMonthRevenue = Invoice::where('status', 'paid')
            ->whereMonth('paid_date', now()->month)
            ->whereYear('paid_date', now()->year)
            ->sum('gross_amount');

        // Przychody w poprzednim miesiącu (do porównania)
        $previousMonthRevenue = Invoice::where('status', 'paid')
            ->whereMonth('paid_date', now()->subMonth()->month)
            ->whereYear('paid_date', now()->subMonth()->year)
            ->sum('gross_amount');

        // Oblicz trend
        $monthlyTrend = $previousMonthRevenue > 0
            ? round((($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100, 1)
            : 0;

        // Przeterminowane faktury
        $overdueInvoices = Invoice::overdue()->count();
        $overdueAmount = Invoice::overdue()->sum('gross_amount');

        // Oczekujące płatności (faktury wystawione ale nieopłacone)
        $pendingPayments = Invoice::whereIn('status', ['issued', 'sent'])
            ->count();
        $pendingAmount = Invoice::whereIn('status', ['issued', 'sent'])
            ->sum('gross_amount');

        return [
            Stat::make('Całkowite przychody', number_format($totalRevenue, 2, ',', ' ').' zł')
                ->description('Wszystkie opłacone faktury')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('Przychody w miesiącu', number_format($currentMonthRevenue, 2, ',', ' ').' zł')
                ->description(($monthlyTrend >= 0 ? '+' : '').$monthlyTrend.'% vs poprzedni miesiąc')
                ->descriptionIcon($monthlyTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($monthlyTrend >= 0 ? 'success' : 'danger'),

            Stat::make('Przeterminowane', $overdueInvoices.' faktur')
                ->description(number_format($overdueAmount, 2, ',', ' ').' zł')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger')
                ->url(route('filament.admin.resources.accounting.invoices.index', ['tableFilters' => ['overdue' => ['isActive' => true]]])),

            Stat::make('Oczekujące płatności', $pendingPayments.' faktur')
                ->description(number_format($pendingAmount, 2, ',', ' ').' zł')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->url(route('filament.admin.resources.accounting.invoices.index', ['tableFilters' => ['unpaid' => ['isActive' => true]]])),
        ];
    }
}
