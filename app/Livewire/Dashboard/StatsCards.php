<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Carbon\Carbon;

class StatsCards extends Component
{
    public $listeners = ['refreshDashboard' => '$refresh'];

    public function getStatsProperty()
    {
        $user = auth()->user();
        $stats = [];

        if ($user->isOwner()) {
            $stats = [
                [
                    'title' => 'Moje pupile',
                    'value' => $user->pets()->count(),
                    'icon' => '🐾',
                    'color' => 'blue',
                    'description' => 'Zarejestrowane zwierzęta'
                ],
                [
                    'title' => 'Aktywne zlecenia',
                    'value' => $user->ownerBookings()
                        ->whereIn('status', ['confirmed', 'in_progress'])
                        ->count(),
                    'icon' => '⏰',
                    'color' => 'green',
                    'description' => 'Potwierdzone rezerwacje'
                ],
                [
                    'title' => 'Wydatki (miesiąc)',
                    'value' => '₽' . number_format($this->getMonthlyExpenses(), 0, ',', ' '),
                    'icon' => '💰',
                    'color' => 'purple',
                    'description' => 'Koszt usług w tym miesiącu'
                ],
                [
                    'title' => 'Ulubieni opiekunowie',
                    'value' => $this->getFavoriteSittersCount(),
                    'icon' => '⭐',
                    'color' => 'yellow',
                    'description' => 'Sprawdzeni specjaliści'
                ]
            ];
        }

        if ($user->isSitter()) {
            $stats = [
                [
                    'title' => 'Moje usługi',
                    'value' => $user->services()->where('is_active', true)->count(),
                    'icon' => '🛠️',
                    'color' => 'blue',
                    'description' => 'Aktywne oferty'
                ],
                [
                    'title' => 'Aktywne zlecenia',
                    'value' => $user->sitterBookings()
                        ->whereIn('status', ['confirmed', 'in_progress'])
                        ->count(),
                    'icon' => '⏰',
                    'color' => 'green',
                    'description' => 'Potwierdzone rezerwacje'
                ],
                [
                    'title' => 'Zarobki (miesiąc)',
                    'value' => '₽' . number_format($this->getMonthlyEarnings(), 0, ',', ' '),
                    'icon' => '💰',
                    'color' => 'purple',
                    'description' => 'Przychód z usług'
                ],
                [
                    'title' => 'Średnia ocena',
                    'value' => number_format($this->getAverageRating(), 1),
                    'icon' => '⭐',
                    'color' => 'yellow',
                    'description' => 'Ocena od klientów'
                ]
            ];
        }

        return $stats;
    }

    private function getMonthlyExpenses()
    {
        $user = auth()->user();
        return $user->ownerBookings()
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->whereIn('status', ['completed', 'paid'])
            ->sum('total_price') ?? 0;
    }

    private function getMonthlyEarnings()
    {
        $user = auth()->user();
        return $user->sitterBookings()
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->whereIn('status', ['completed', 'paid'])
            ->sum('total_price') ?? 0;
    }

    private function getFavoriteSittersCount()
    {
        // TODO: Implement favorites system
        return auth()->user()->ownerBookings()
            ->distinct('sitter_id')
            ->count('sitter_id');
    }

    private function getAverageRating()
    {
        // TODO: Implement reviews system
        return 4.8; // Placeholder
    }

    public function render()
    {
        return view('livewire.dashboard.stats-cards');
    }
}