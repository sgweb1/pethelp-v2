<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

/**
 * Widget wyświetlający statystyki płatności w panelu administracyjnym.
 *
 * Pokazuje kluczowe metryki dotyczące płatności:
 * - Całkowite przychody
 * - Przychody w tym miesiącu
 * - Liczba oczekujących płatności
 * - Całkowita prowizja
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class PaymentsOverviewWidget extends StatsOverviewWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        // Całkowite przychody (completed payments)
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');

        // Przychody w tym miesiącu
        $monthlyRevenue = Payment::where('status', 'completed')
            ->whereMonth('processed_at', now()->month)
            ->whereYear('processed_at', now()->year)
            ->sum('amount');

        // Liczba oczekujących płatności
        $pendingPayments = Payment::where('status', 'pending')->count();

        // Całkowita prowizja (commission)
        $totalCommission = Payment::where('status', 'completed')->sum('commission');

        // Przychody w poprzednim miesiącu (do obliczenia trendu)
        $lastMonthRevenue = Payment::where('status', 'completed')
            ->whereMonth('processed_at', now()->subMonth()->month)
            ->whereYear('processed_at', now()->subMonth()->year)
            ->sum('amount');

        // Oblicz trend (zmiana % względem poprzedniego miesiąca)
        $revenueChange = $lastMonthRevenue > 0
            ? (($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100
            : 0;

        return [
            // Całkowite przychody
            Stat::make('Całkowite przychody', Number::currency($totalRevenue, 'PLN'))
                ->description('Suma wszystkich zakończonych płatności')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            // Przychody w tym miesiącu
            Stat::make('Przychody w tym miesiącu', Number::currency($monthlyRevenue, 'PLN'))
                ->description($revenueChange >= 0
                    ? sprintf('+%.1f%% w stosunku do poprzedniego miesiąca', abs($revenueChange))
                    : sprintf('-%.1f%% w stosunku do poprzedniego miesiąca', abs($revenueChange))
                )
                ->descriptionIcon($revenueChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueChange >= 0 ? 'success' : 'danger')
                ->chart($this->getMonthlyRevenueChart()),

            // Oczekujące płatności
            Stat::make('Oczekujące płatności', $pendingPayments)
                ->description('Płatności do przetworzenia')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingPayments > 10 ? 'warning' : 'gray'),

            // Całkowita prowizja
            Stat::make('Całkowita prowizja', Number::currency($totalCommission, 'PLN'))
                ->description('Suma prowizji z zakończonych płatności')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info'),
        ];
    }

    /**
     * Generuje dane wykresu przychodów z ostatnich 7 dni.
     *
     * @return array<int, float> Tablica przychodów z ostatnich 7 dni
     */
    protected function getMonthlyRevenueChart(): array
    {
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenue = Payment::where('status', 'completed')
                ->whereDate('processed_at', $date->format('Y-m-d'))
                ->sum('amount');

            $data[] = (float) $revenue;
        }

        return $data;
    }
}
