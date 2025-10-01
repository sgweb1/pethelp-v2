<?php

namespace App\Livewire\Dashboard\Widgets;

use App\Livewire\Dashboard\Core\BaseDashboard;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Widget statystyk dashboard dla różnych typów użytkowników.
 *
 * Generuje i wyświetla kluczowe metryki biznesowe dostosowane do roli użytkownika.
 * Obsługuje właścicieli zwierząt, opiekunów oraz użytkowników z podwójną rolą.
 * Zawiera funkcjonalność cache'owania i automatycznego odświeżania danych.
 *
 * @author Claude AI Assistant
 *
 * @since 1.0.0
 */
class StatsWidget extends BaseDashboard
{
    /**
     * Typ statystyk do wyświetlenia ('owner'|'sitter'|'combined').
     */
    public string $type = 'auto';

    /**
     * Czy pokazać porównanie z poprzednim okresem.
     */
    public bool $showTrends = true;

    /**
     * Okres dla obliczeń trendu (7|30|90 dni).
     */
    public int $trendPeriod = 30;

    /**
     * Layout statystyk ('grid-2'|'grid-3'|'grid-4'|'auto').
     */
    public string $layout = 'auto';

    /**
     * Inicjalizacja widgetu z parametrami.
     */
    public function mount(
        string $type = 'auto',
        bool $showTrends = true,
        int $trendPeriod = 30,
        string $layout = 'auto'
    ): void {
        parent::mount();

        $this->type = $type;
        $this->showTrends = $showTrends;
        $this->trendPeriod = $trendPeriod;
        $this->layout = $layout;

        // Auto-detect typu na podstawie roli użytkownika
        if ($this->type === 'auto') {
            $user = $this->getUser();
            if ($user->isOwner() && $user->isSitter()) {
                $this->type = 'combined';
            } elseif ($user->isOwner()) {
                $this->type = 'owner';
            } elseif ($user->isSitter()) {
                $this->type = 'sitter';
            } else {
                $this->type = 'basic';
            }
        }
    }

    /**
     * Pobiera dane statystyk na podstawie typu użytkownika.
     */
    protected function getData(): Collection
    {
        $user = $this->getUser();

        return match ($this->type) {
            'owner' => $this->getOwnerStats($user),
            'sitter' => $this->getSitterStats($user),
            'combined' => $this->getCombinedStats($user),
            'basic' => $this->getBasicStats($user),
            default => collect()
        };
    }

    /**
     * Generuje statystyki dla właściciela zwierząt.
     */
    protected function getOwnerStats(\App\Models\User $user): Collection
    {
        $stats = collect();

        // Statystyka zwierząt
        $petsCount = $user->pets()->count();
        $petsCountPrevious = $this->showTrends ? $this->getPreviousPetsCount($user) : $petsCount;

        $stats->push([
            'title' => 'Moje zwierzęta',
            'value' => $this->formatNumber($petsCount),
            'icon' => '🐾',
            'color' => 'blue',
            'description' => 'Zarejestrowane pupile',
            'trend' => $this->showTrends ? $this->calculateTrend($petsCount, $petsCountPrevious) : null,
            'route' => 'profile.pets.index',
        ]);

        // Aktywne rezerwacje
        $activeBookings = $user->ownerBookings()
            ->whereIn('status', ['confirmed', 'in_progress'])
            ->count();

        $stats->push([
            'title' => 'Aktywne zlecenia',
            'value' => $this->formatNumber($activeBookings),
            'icon' => '⏰',
            'color' => 'green',
            'description' => 'Potwierdzone rezerwacje',
            'route' => 'profile.bookings',
        ]);

        // Wydatki miesięczne
        $monthlyExpenses = $this->getMonthlyExpenses($user);
        $previousMonthExpenses = $this->showTrends ? $this->getPreviousMonthExpenses($user) : $monthlyExpenses;

        $stats->push([
            'title' => 'Wydatki (miesiąc)',
            'value' => $this->formatCurrency($monthlyExpenses),
            'icon' => '💰',
            'color' => 'purple',
            'description' => 'Koszt usług w tym miesiącu',
            'trend' => $this->showTrends ? $this->calculateTrend($monthlyExpenses, $previousMonthExpenses) : null,
        ]);

        // Średnia ocena otrzymanych usług
        $averageRating = $this->getAverageReceivedRating($user);

        $stats->push([
            'title' => 'Średnia ocena',
            'value' => number_format($averageRating, 1),
            'icon' => '⭐',
            'color' => 'yellow',
            'description' => 'Twoja ocena usług',
            'route' => 'profile.reviews',
        ]);

        return $stats;
    }

    /**
     * Generuje statystyki dla opiekuna zwierząt.
     */
    protected function getSitterStats(\App\Models\User $user): Collection
    {
        $stats = collect();

        // Aktywne usługi
        $activeServices = $user->services()->where('is_active', true)->count();
        $previousActiveServices = $this->showTrends ? $this->getPreviousActiveServices($user) : $activeServices;

        $stats->push([
            'title' => 'Moje usługi',
            'value' => $this->formatNumber($activeServices),
            'icon' => '🛠️',
            'color' => 'blue',
            'description' => 'Aktywne oferty',
            'trend' => $this->showTrends ? $this->calculateTrend($activeServices, $previousActiveServices) : null,
            'route' => 'profile.services.index',
        ]);

        // Aktywne zlecenia jako sitter
        $activeBookings = $user->sitterBookings()
            ->whereIn('status', ['confirmed', 'in_progress'])
            ->count();

        $stats->push([
            'title' => 'Aktywne zlecenia',
            'value' => $this->formatNumber($activeBookings),
            'icon' => '⏰',
            'color' => 'green',
            'description' => 'Potwierdzone rezerwacje',
            'route' => 'profile.bookings',
        ]);

        // Zarobki miesięczne
        $monthlyEarnings = $this->getMonthlyEarnings($user);
        $previousMonthEarnings = $this->showTrends ? $this->getPreviousMonthEarnings($user) : $monthlyEarnings;

        $stats->push([
            'title' => 'Zarobki (miesiąc)',
            'value' => $this->formatCurrency($monthlyEarnings),
            'icon' => '💰',
            'color' => 'purple',
            'description' => 'Przychód z usług',
            'trend' => $this->showTrends ? $this->calculateTrend($monthlyEarnings, $previousMonthEarnings) : null,
        ]);

        // Średnia ocena jako sitter
        $averageRating = $this->getAverageRating($user);

        $stats->push([
            'title' => 'Średnia ocena',
            'value' => number_format($averageRating, 1),
            'icon' => '⭐',
            'color' => 'yellow',
            'description' => 'Ocena od klientów',
            'route' => 'profile.reviews',
        ]);

        return $stats;
    }

    /**
     * Generuje kombinowane statystyki dla użytkowników z podwójną rolą.
     */
    protected function getCombinedStats(\App\Models\User $user): Collection
    {
        $ownerStats = $this->getOwnerStats($user);
        $sitterStats = $this->getSitterStats($user);

        // Merge i deduplikacja statystyk
        $combined = collect();

        // Zwierzęta (tylko dla właściciela)
        $combined->push($ownerStats->first());

        // Usługi (tylko dla opiekuna)
        $combined->push($sitterStats->first());

        // Łączne aktywne zlecenia
        $ownerBookings = $user->ownerBookings()->whereIn('status', ['confirmed', 'in_progress'])->count();
        $sitterBookings = $user->sitterBookings()->whereIn('status', ['confirmed', 'in_progress'])->count();
        $totalBookings = $ownerBookings + $sitterBookings;

        $combined->push([
            'title' => 'Wszystkie zlecenia',
            'value' => $this->formatNumber($totalBookings),
            'icon' => '⏰',
            'color' => 'green',
            'description' => "Właściciel: {$ownerBookings}, Opiekun: {$sitterBookings}",
            'route' => 'profile.bookings',
        ]);

        // Bilans finansowy (zarobki - wydatki)
        $monthlyEarnings = $this->getMonthlyEarnings($user);
        $monthlyExpenses = $this->getMonthlyExpenses($user);
        $balance = $monthlyEarnings - $monthlyExpenses;

        $combined->push([
            'title' => 'Bilans (miesiąc)',
            'value' => $this->formatCurrency($balance),
            'icon' => $balance >= 0 ? '📈' : '📉',
            'color' => $balance >= 0 ? 'green' : 'red',
            'description' => $balance >= 0 ? 'Dodatni bilans' : 'Ujemny bilans',
        ]);

        return $combined;
    }

    /**
     * Generuje podstawowe statystyki dla nowych użytkowników.
     */
    protected function getBasicStats(\App\Models\User $user): Collection
    {
        return collect([
            [
                'title' => 'Profil',
                'value' => $user->profile ? '✓' : '○',
                'icon' => '👤',
                'color' => $user->profile ? 'green' : 'yellow',
                'description' => $user->profile ? 'Uzupełniony' : 'Do uzupełnienia',
                'route' => 'profile.edit',
            ],
            [
                'title' => 'Aktywność',
                'value' => $user->created_at->diffInDays().' dni',
                'icon' => '📅',
                'color' => 'blue',
                'description' => 'Na platformie od',
            ],
            [
                'title' => 'Wiadomości',
                'value' => $this->formatNumber($user->getUnreadMessagesCount()),
                'icon' => '💬',
                'color' => 'indigo',
                'description' => 'Nieprzeczytane',
                'route' => 'profile.chat.index',
            ],
            [
                'title' => 'Powiadomienia',
                'value' => $this->formatNumber($user->notifications()->unread()->count()),
                'icon' => '🔔',
                'color' => 'purple',
                'description' => 'Nowe powiadomienia',
                'route' => 'profile.notifications',
            ],
        ]);
    }

    // Metody pomocnicze dla obliczeń statystyk

    private function getMonthlyExpenses(\App\Models\User $user): float
    {
        return $user->ownerBookings()
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->whereIn('status', ['completed', 'paid'])
            ->sum('total_price') ?? 0;
    }

    private function getPreviousMonthExpenses(\App\Models\User $user): float
    {
        $previousMonth = Carbon::now()->subMonth();

        return $user->ownerBookings()
            ->whereMonth('created_at', $previousMonth->month)
            ->whereYear('created_at', $previousMonth->year)
            ->whereIn('status', ['completed', 'paid'])
            ->sum('total_price') ?? 0;
    }

    private function getMonthlyEarnings(\App\Models\User $user): float
    {
        return $user->sitterBookings()
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->whereIn('status', ['completed', 'paid'])
            ->sum('total_price') ?? 0;
    }

    private function getPreviousMonthEarnings(\App\Models\User $user): float
    {
        $previousMonth = Carbon::now()->subMonth();

        return $user->sitterBookings()
            ->whereMonth('created_at', $previousMonth->month)
            ->whereYear('created_at', $previousMonth->year)
            ->whereIn('status', ['completed', 'paid'])
            ->sum('total_price') ?? 0;
    }

    private function getPreviousPetsCount(\App\Models\User $user): int
    {
        $dateThreshold = Carbon::now()->subDays($this->trendPeriod);

        return $user->pets()->where('created_at', '<', $dateThreshold)->count();
    }

    private function getPreviousActiveServices(\App\Models\User $user): int
    {
        $dateThreshold = Carbon::now()->subDays($this->trendPeriod);

        return $user->services()
            ->where('is_active', true)
            ->where('created_at', '<', $dateThreshold)
            ->count();
    }

    private function getAverageRating(\App\Models\User $user): float
    {
        // TODO: Implementować system ocen
        return 4.8; // Placeholder
    }

    private function getAverageReceivedRating(\App\Models\User $user): float
    {
        // TODO: Implementować system ocen jako klient
        return 4.5; // Placeholder
    }

    /**
     * Właściwość computed dla statystyk.
     */
    public function getStatsProperty(): Collection
    {
        return $this->getCachedData();
    }

    /**
     * Renderuje widget statystyk.
     */
    public function render(): \Illuminate\View\View
    {
        return view('livewire.dashboard.widgets.stats-widget');
    }
}
