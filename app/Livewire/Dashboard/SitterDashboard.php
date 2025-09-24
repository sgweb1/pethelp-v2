<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class SitterDashboard extends Component
{
    public bool $isOnline = true;
    public bool $isAvailableToday = true;

    public function toggleOnlineStatus()
    {
        $this->isOnline = !$this->isOnline;
        // TODO: Update database status
        session()->flash('message', $this->isOnline ? 'Status zmieniony na: Online' : 'Status zmieniony na: Offline');
    }

    public function toggleAvailability()
    {
        $this->isAvailableToday = !$this->isAvailableToday;
        // TODO: Update database availability
        session()->flash('message', $this->isAvailableToday ? 'Dostępność: Dostępny dziś' : 'Dostępność: Niedostępny dziś');
    }

    public function getStatsProperty()
    {
        $user = auth()->user();
        return [
            'services' => $user->services()->count(),
            'active_services' => $user->services()->where('is_active', true)->count(),
            'bookings' => $user->sitterBookings()->count(),
            'active_bookings' => $user->sitterBookings()->whereIn('status', ['pending', 'confirmed', 'in_progress'])->count(),
            'today_bookings' => $user->sitterBookings()->whereDate('start_date', today())->count(),
            'reviews' => $user->reviewsReceived()->count(),
            'profile_views' => 0, // TODO: implement profile views tracking
            'completion_rate' => 95, // TODO: calculate actual completion rate
        ];
    }

    public function getAverageRatingProperty()
    {
        $reviews = auth()->user()->reviewsReceived();
        return $reviews->count() > 0 ? $reviews->avg('rating') : 0;
    }

    public function getTodayEarningsProperty()
    {
        // TODO: implement earnings calculation
        return 0;
    }

    public function getThisMonthEarningsProperty()
    {
        // TODO: implement monthly earnings calculation
        return 0;
    }

    public function getRecentBookingsProperty()
    {
        return auth()->user()->sitterBookings()
            ->with(['service.category', 'owner', 'pet'])
            ->latest()
            ->take(3)
            ->get();
    }

    public function getTodayBookingsProperty()
    {
        return auth()->user()->sitterBookings()
            ->with(['service', 'owner', 'pet'])
            ->whereDate('start_date', today())
            ->whereIn('status', ['confirmed', 'in_progress'])
            ->orderBy('start_date')
            ->get();
    }

    public function getUpcomingBookingsProperty()
    {
        return auth()->user()->sitterBookings()
            ->with(['service', 'owner', 'pet'])
            ->where('start_date', '>', now())
            ->whereIn('status', ['confirmed'])
            ->orderBy('start_date')
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard.sitter-dashboard');
    }
}