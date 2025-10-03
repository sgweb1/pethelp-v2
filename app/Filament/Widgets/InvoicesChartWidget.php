<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Filament\Widgets\ChartWidget;

/**
 * Widget wykresu faktur.
 *
 * Wyświetla wykres słupkowy pokazujący przychody z faktur
 * w ostatnich 6 miesiącach z podziałem na statusy.
 *
 * @author Claude AI Assistant
 * @since 1.0.0
 */
class InvoicesChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Przychody z faktur (ostatnie 6 miesięcy)';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $months = collect();

        // Zbierz dane za ostatnie 6 miesięcy
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->translatedFormat('M Y');

            $paidAmount = Invoice::where('status', 'paid')
                ->whereMonth('paid_date', $date->month)
                ->whereYear('paid_date', $date->year)
                ->sum('gross_amount');

            $issuedAmount = Invoice::whereIn('status', ['issued', 'sent'])
                ->whereMonth('issue_date', $date->month)
                ->whereYear('issue_date', $date->year)
                ->sum('gross_amount');

            $months->push([
                'month' => $monthName,
                'paid' => $paidAmount,
                'issued' => $issuedAmount,
            ]);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Opłacone',
                    'data' => $months->pluck('paid')->toArray(),
                    'backgroundColor' => '#10b981',
                    'borderColor' => '#059669',
                ],
                [
                    'label' => 'Wystawione (nieopłacone)',
                    'data' => $months->pluck('issued')->toArray(),
                    'backgroundColor' => '#f59e0b',
                    'borderColor' => '#d97706',
                ],
            ],
            'labels' => $months->pluck('month')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => "function(value) { return value.toLocaleString('pl-PL') + ' zł'; }",
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
